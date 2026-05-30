<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\{JsonResponse, Request};
use App\Classes\ApiResponseClass;
use App\Helpers\API\{CommonHelper,BasicHelper};
use Illuminate\Support\Facades\{DB,Session, Auth, Hash, Validator};
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use App\Interfaces\User\{
    UserRepositoryInterface ,KycRepositoryInterface, UserDetailsRepositoryInterface
};
use App\Interfaces\{
    MailTemplatesRepositoryInterface
};
use App\Http\Resources\User\{
    ProfileResource
};


class ProfileController extends BaseController
{    
    public $unauthorizedStatus   =  CommonHelper::UNAUTHORIZED;

    private UserRepositoryInterface $userRepositoryInterface;
    private MailTemplatesRepositoryInterface $mailTemplatesRepositoryInterface; 
    private KycRepositoryInterface $kycRepositoryInterface;   
    private UserDetailsRepositoryInterface $userDetailsRepositoryInterface;  

    public function __construct(UserRepositoryInterface $userRepositoryInterface, MailTemplatesRepositoryInterface $mailTemplatesRepositoryInterface, KycRepositoryInterface $kycRepositoryInterface, UserDetailsRepositoryInterface $userDetailsRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->mailTemplatesRepositoryInterface = $mailTemplatesRepositoryInterface;
        $this->kycRepositoryInterface = $kycRepositoryInterface;
        $this->userDetailsRepositoryInterface = $userDetailsRepositoryInterface;

    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $token = Auth::user()->token();
            $token->revoke();
            return $this->sendResponse(__('custom_messages.logout_successfully'));
        }else{
            return $this->sendError(__('custom_messages.unauthorized'), ['error'=>'Unauthorized'], $this->unauthorizedStatus);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->sendError('Current password is incorrect.', ['error'=>'Current password is incorrect.']);
        }

        $validator = Validator::make($request->all(), [
            'new_password'     => ['required', 'string', 'min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/','regex:/[@$!%*?&^#]/','max:16','confirmed']
        ],[
            'new_password.min' => 'The new password must be at least 8 characters.',
            'new_password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        $newPassword = $request->new_password;

        // Check if the new password matches the last three passwords
        if (CommonHelper::passwordMatchesHistory($user, $newPassword)) {
            return $this->sendError('The new password cannot match any of the last three passwords.', ['error'=>'The new password cannot match any of the last three passwords.']);
        }

        $user_password = Hash::make($newPassword);

        $password_history = CommonHelper::updateHistory($user->password_history, $user_password);

        $updateDetails = [
            'password_changed_at' =>Carbon::now(),
            'password' => $user_password,
            'password_history' => $password_history
        ];

        $this->userRepositoryInterface->update($user->id,$updateDetails);

        $mail_content = $this->mailTemplatesRepositoryInterface->getByColumn(['status' => 1,'slug' => 'change_password']);
        if($mail_content)
        {
            $href = config('app.frontend_url').'/login';
            $email = $user->email;
            $greeting = $name = $user->full_name;
            $domain_ltd =  CommonHelper::getMetaData('site_name');
            $content = $mail_content->body;
            $subject = $mail_content->subject;

            $formattedContent = str_replace(
                ['{{NAME}}','{{DOMAIN}}','{{LINK}}'],
                [$name,$domain_ltd],
                $content
            );
            
            CommonHelper::sendMail($email, $subject, $greeting, $formattedContent);
        }
        activity('password')
        ->performedOn($user)
        ->causedBy($user)
        ->withProperties([
            'ip' => request()->ip(),
        ])->log(__('activity_logs.password_changed'));

        return $this->sendResponse( 'Password changed successfully.',null);
    }

    public function reSendOTP(Request $request)
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            
            $data['user_id'] = $user->id;
            $otp = BasicHelper::sendEmailOtp($user, $this->kycRepositoryInterface, $this->mailTemplatesRepositoryInterface);
            if($otp['status'] == false ){
                DB::rollBack();
                return $this->sendError($otp['message'],[], $this->validationErrorStatus);
            }
            DB::commit();
            return ApiResponseClass::sendResponseCode($otp['message'] , $this->successStatus);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' =>'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        $user = Auth::user();

        DB::beginTransaction();
        try{
            $otp_details = $this->kycRepositoryInterface->getByColumn(['user_id' => $user->id]);
            if($otp_details)
            {

                if(($otp_details->email_verification_status == 1) && ($otp_details->email_otp == null)){
                    return ApiResponseClass::sendResponseCode(__('custom_messages.already_verified'));
                }

                if( $otp_details->email_otp != $request->otp){
                    $email_attempt_limit = CommonHelper::getMetaData('email_attempt');
                    $no_of_attempts = $otp_details->no_of_attempts + 1;
                    if ($no_of_attempts >= $email_attempt_limit) {
                        $now = Carbon::now();
                        $email_validity_time = (int) CommonHelper::getMetaData('email_validity'); // in minutes
                      
                        $newBlockTime = $now->copy()->addMinutes($email_validity_time);
                       
                        if(!empty($otp_details->block_time)){
                            $blockTime = Carbon::parse($otp_details->block_time);
                            if ($now->greaterThan($blockTime)) {
                                $this->kycRepositoryInterface->updateByColumn(['user_id' => $user->id], ['block_time' => $newBlockTime]);
                                DB::commit();
                                return [
                                    'status' => false,
                                    'message' => "You have reached the maximum number of attempts. Please try again after {$email_validity_time} minutes."
                                ];
                            }else{
                                $remainingMinutes = $now->diffInMinutes($blockTime);
                                return [
                                    'status' => false,
                                    'message' => "You have reached the maximum number of attempts. Please try again after {$remainingMinutes} minutes."
                                ];
                                
                            }
                        }else{
                            $this->kycRepositoryInterface->updateByColumn(['user_id' => $user->id], ['block_time' => $newBlockTime]);
                            DB::commit();
                            return [
                                'status' => false,
                                'message' => "You have reached the maximum number of attempts. Please try again after {$email_validity_time} minutes."
                            ];

                        }
                        
                    }

                    $this->kycRepositoryInterface->updateByColumn(['user_id' => $user->id], ['no_of_attempts' => $no_of_attempts]);
                    DB::commit();
                    return $this->sendError('Invalid OTP. Please try again.','Invalid OTP. Please try again.');
                }

                $update_data['email_verification_status'] = 1;
                $update_data['email_verify_at'] = Carbon::now();
                $update_data['email_otp'] = NULL;

                $update_details = $this->KycRepositoryInterface->update($user->id,$update_data);

                if($update_details)
                {
                    $this->userRepositoryInterface->update($user->id,['stage_status' => 2, 'is_email_verified' => 1]);
                }

                DB::commit();
                return ApiResponseClass::sendResponse(null, $this->successStatus, 'Verification successfully.', );
            }else{
                return $this->sendError('Please send OTP first.','Please send OTP first');
            }
        }catch(\Exception $ex){

            return ApiResponseClass::rollback($ex);
        }
    }

    public function profile()
    {
        $user_data = Auth::user();
        if(!empty($user_data)){

            $user_data = $this->userRepositoryInterface->getByColumn(['id' => $user_data->id]);
            $avatar = $user_data->avatar ?? null;
            $user_data->avatar = CommonHelper::displayImage($avatar, CommonHelper::PROFILES);

            return ApiResponseClass::sendResponseCode(new ProfileResource($user_data),$this->successStatus ,'Profile data found.');
        }
        return $this->sendError('Profile not found.','',$this->notFoundStatus);
    }

    public function updateProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|file|mimes:png,jpg,jpeg,gif,bmp|max:2048',
        ], [
            'image.required' => 'An image file is required.',
            'image.file' => 'The uploaded file must be a valid file.',
            'image.mimes' => 'The image must be a file of type: png, jpg, jpeg, gif, bmp.',
            'image.max' => 'The image may not be greater than 2MB.',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $updateDetails = [];
            if ($request->hasFile('profile_image')) {
                $profile = CommonHelper::uploadImage($request->file('profile_image'), true, CommonHelper::PROFILES);
                $updateDetails['avatar'] = $profile;
            }

            $result = $this->userRepositoryInterface->update($user->id,$updateDetails);

            if ($result) {
                DB::commit();

                activity('profile_updated')
                ->performedOn($user)
                ->causedBy($user)
                ->withProperties([
                    'ip' => request()->ip(),
                ])
                ->log(__('activity_logs.user_profile_updated'));

                return ApiResponseClass::sendResponseCode(null, $this->noContentStatus, 'Profile Image updated successfully.' );
            } else {
                DB::rollBack();
                return $this->sendError('Error in update. Please try again.','Error in update. Please try again.');
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::rollback($ex);
        }
    }

    public function updateProfileDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'first_name'    => 'required|regex:/^[a-zA-Z]+(?:[\s\-][a-zA-Z]+)*$/',
                'last_name'     => 'required|regex:/^[a-zA-Z]+(?:[\s\-][a-zA-Z]+)*$/',
                'username'      => 'required|string|min:8|max:16|regex:/[a-z]/|regex:/[0-9]/|unique:users,username',
                'mobile'        => 'nullable|unique:users,mobile|valid_phone_length:' . $request->country_code,
                'dial_code'     => 'nullable|exists:countries,dial_code',
                'address'       => 'nullable|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
                'country_id'    => 'nullable|exists:countries,id',
                'state_id'      => 'nullable|exists:states,id',
                'city_id'       => 'nullable|exists:cities,id',
                'zipcode'       => 'nullable|string|regex:/^[a-zA-Z0-9\- ]+$/',
                'dob'           => 'nullable|date|before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
                'gender'        => 'nullable|in:male,female,other',
            ]
        );

        if($validator->fails()){
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            $updateUser = collect(['first_name','last_name','mobile', 'country_code', 'dial_code'])
                ->mapWithKeys(fn($field) => [$field => $request->get($field, $user->$field ?? null)])
                ->toArray();

            $result = $this->userRepositoryInterface->update($user->id,$updateUser);

            $updateUserDetails = collect(['address','country_id','state_id', 'city_id', 'zipcode', 'dob', 'gender'])
                ->filter(fn($field) => $request->has($field))
                ->mapWithKeys(fn($field) => [$field => $request->$field])
                ->toArray();

            $result = $this->userRepositoryInterface->update($user->id,$updateUser);

            DB::commit();
            return ApiResponseClass::sendResponseCode(null, $this->noContentStatus, 'User updated successfully.');

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }
    

}
