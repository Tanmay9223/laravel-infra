<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\{
    Auth, Hash, DB , Mail, Validator, Password
};
use App\Classes\ApiResponseClass;
use App\Helpers\API\{BasicHelper, CommonHelper, TradingHelper};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use App\Interfaces\User\{
    UserRepositoryInterface, KycRepositoryInterface, UserDetailsRepositoryInterface
};
use App\Interfaces\{
    MailTemplatesRepositoryInterface
};


class AuthController extends BaseController
{
    public $successStatus        =  CommonHelper::SUCCESS_STATUS;
    public $createSuccessStatus  =  CommonHelper::CREATE_SUCCESS_STATUS;
    public $noContentStatus      =  CommonHelper::NO_CONTENT_STATUS;
    public $unauthorizedStatus   =  CommonHelper::UNAUTHORIZED;
    public $forbiddenStatus      =  CommonHelper::FORBIDDEN;

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

    public function initializeData(): JsonResponse
    {
        $other_links = [
            [
                'title' => 'Privacy Policy',
                'url' => CommonHelper::getMetaData('privacy_policy_link'),
            ],
            [
                'title' => 'Terms and Conditions',
                'url' => CommonHelper::getMetaData('terms_condition_partner'),
            ],
            [
                'title' => 'Risk Warning',
                'url'=> '#',
            ],
            [
                'title' => 'Charebacks & Refund',
                'url'=> '#',
            ],
            [
                'title' => 'Customer Service Agreement',
                'url'=> '#',
            ],
            [
                'title' => 'AML Policy',
                'url'=> '#',
            ]
        ];
        $logo_array = [
            'light_logo'=> env('APP_URL')."/default_images/logo/light_logo.png",
            "dark_logo"=> env('APP_URL')."/default_images/logo/dark_logo.png",

        ];

        $site_name = CommonHelper::getMetaData('site_name') ?? env('APP_NAME');
        $domain = CommonHelper::getMetaData('website_url') ?? env('APP_URL');

        $data['name'] = $site_name;
        $data['logo'] = $logo_array;
        $data['sidebar_logo'] = env('APP_URL')."/default_images/logo/sidebar_logo.png";
        $data['favicon'] = env('APP_URL')."/default_images/logo/favicon.png";
        $data['footer_text'] = "<p>Risk statement: An investment in derivatives may mean investors may lose an amount even greater than their original investment. Anyone wishing to invest in any of the products mentioned in " .env('APP_URL')." should seek their own financial or professional advice. Trading of securities, forex, stock market, commodities, options and futures may not be suitable for everyone and involves the risk of losing part or all of your money. Trading in the financial markets has large potential rewards, but also large potential risk. You must be aware of the risks and be willing to accept them in order to invest in the markets. Don't invest and trade with money which you can't afford to lose. Forex Trading are not allowed in some countries, before investing your money, make sure whether your country is allowing this or not.</p><p>The website is operated by ".env('APP_NAME')." Ltd; ".env('APP_NAME')." Ltd is registered under Republic of Mauritius as a Mauritius Authorized Company with registration number 111111. ".env('APP_NAME')." Ltd under the name of ".env('APP_NAME')." Global Limited is authorized and regulated in Mauritius by the Financial Services Commission (FSC), License Number: GB1111111. ".env('APP_NAME')." Global Limited is registered under Republic of Mauritius with company number: 111111</p>";
        $data['other_links'] = $other_links;

        return ApiResponseClass::sendResponseCode($data, $this->successStatus,__('custom_messages.initialize_data'), );
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|regex:/^[a-zA-Z]+(?:[\s\-][a-zA-Z]+)*$/',
            'last_name' => 'required|regex:/^[a-zA-Z]+(?:[\s\-][a-zA-Z]+)*$/',
            'email' => 'required|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|email:dns|unique:users,email',
            'password' => 'required|confirmed|string|min:8|max:16|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*?&^#]/',
        ],
            [
            'password.regex' => 'Password must be 8-16 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        $sponsor_id = null;
        if (!empty($request->sponsor_by)) {
            $sponsor_id = BasicHelper::decryptData($request->sponsor_by);

            $sponsor =  $this->userRepositoryInterface->getByColumn(['sponsor_id' => $sponsor_id,'status' => 1]);
            if (!$sponsor) {
                return $this->sendError(__('custom_messages.invalid_sponsor_by'), __('custom_messages.invalid_sponsor_by'));
            }
        }

        $username = CommonHelper::generateUniqueUsername($request->first_name,$this->userRepositoryInterface);

        $password = Hash::make($request->password);

        $data = [
            'uuid' => Str::uuid(),
            'username'   => $username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $password,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'password_history' => $password,
            'status' => 1,
            'stage_status' => 1,
            'sponsor_id' => CommonHelper::getRandString(6),
            'sponsor_by' => $sponsor->id ?? null,
            'password_changed_at' => Carbon::now(),
        ];

        

        DB::beginTransaction();

        try {
            
            $user = $this->userRepositoryInterface->store($data);

            $deatils_data =[
                        'user_id' => $user->id,
            ];

            $user_deatils = $this->userDetailsRepositoryInterface->store($deatils_data);

            if ($user) {
                $token =   $user->createToken('User', ['user'])->accessToken;

                if((isset($request->sponsor_by)) && (!is_null($request->sponsor_by)))
                {
                    $mail_content = $this->mailTemplatesRepositoryInterface->getByColumn(['status' => 1,'slug' => 'user_created_by_referral']);
                    if($mail_content)
                    {
                        $email = $sponsor->email;
                        $date = date("d M, Y");
                        $greeting = $sponsor->full_name;
                        $name = $request->first_name. ' '.$request->last_name;
                        $domain_ltd =  CommonHelper::getMetaData('site_name');
                        $content = $mail_content->body;
                        $subject = $mail_content->subject;

                        $formattedSubject = str_replace(
                            ['{{DOMAIN}}'],
                            [$domain_ltd],
                            $subject
                        );

                        $formattedContent = str_replace(
                            ['{{NAME}}','{{EMAIL}}', '{{DATE}}','{{DOMAIN}}'],
                            [$name,$data['email'], $date,$domain_ltd],
                            $content
                        );
                        CommonHelper::sendMail($email, $formattedSubject, $greeting, $formattedContent);
                    }
                }

                $mail_content = $this->mailTemplatesRepositoryInterface->getByColumn(['status' => 1,'slug' => 'user_registered']);
                if ($mail_content) {

                    $email = $data['email'];
                    $greeting = $name = $data['first_name'] . ' ' . $data['last_name'];
                    $domain_ltd =  CommonHelper::getMetaData('site_name');
                    $content = $mail_content->body;
                    $subject = $mail_content->subject;

                    $formattedSubject = str_replace(
                        ['{{DOMAIN}}'],
                        [$domain_ltd],
                        $subject
                    );

                    $formattedContent = str_replace(
                        ['{{NAME}}', '{{DOMAIN}}'],
                        [$name, $domain_ltd],
                        $content
                    );
                    CommonHelper::sendMail($email, $formattedSubject, $greeting, $formattedContent);
                }
            }

            $require_email_verification =  CommonHelper::getMetaData('require_email_verification');
            if((isset($require_email_verification)) && (!is_null($require_email_verification)) && ($require_email_verification == true)){
                $response['require_email_verification'] = true;
                
                $otp = BasicHelper::sendEmailOtp($user, $this->kycRepositoryInterface, $this->mailTemplatesRepositoryInterface);
                
            } else{
                $response['require_email_verification'] = false;
            }
            $response['is_email_verified'] = false;

            DB::commit();
            $response['token'] = $token;
            return $this->sendResponse(__('custom_messages.register_successfully'), $response);

        } catch (\Exception $e) {

            DB::rollBack();
            return $this->sendError('Registration failed.', ['error' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        $user =  $this->userRepositoryInterface->getByColumn(['email' => $request->email]);
        if ($user) {
            if($user->status == 0)
            {
                return $this->sendError(__('custom_messages.inactive_user_error'),__('custom_messages.inactive_user_error'));
            }

            if(Hash::check($request->password, $user->password)){

                // Revoke all existing tokens for the user
                // $user->tokens()->where('revoked', false)->update(['revoked' => true]);

                $user->save();

                $success['first_name']    =   $user->first_name;
                $success['last_name']     =   $user->last_name;
                $success['email']         =   $user->email;
                $success['token']         =   $user->createToken('User', ['user'])->accessToken;


                $require_email_verification =  CommonHelper::getMetaData('require_email_verification');
                if((isset($require_email_verification)) && (!is_null($require_email_verification)) && ($require_email_verification == true))
                {
                    $success['require_email_verification'] = true;

                    if($user->is_email_verified === 1){
                        $success['is_email_verified'] = true;
                    } else {
                        
                        $success['is_email_verified'] = false;
                    }

                } else
                {
                    $success['require_email_verification'] = false;
                    $success['is_email_verified'] = false;
                }

                $this->logUserLoginActivity($user);

                return $this->sendResponse('Login Successfully.', $success);
            }else{
                return $this->sendError('Password Invalid. Please try again.');
            }
        } else {
            return $this->sendError('You are not register or invalid user');
        }
    }

    public function forgotPassword(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        $isUser = $exists = $this->userRepositoryInterface->dataExists('email', $input['email']); 
        
        if ($isUser) {

            $broker = $isUser ? 'users' : '';
            $response = Password::broker($broker)->sendResetLink($input);
            if ($response == Password::RESET_LINK_SENT) {
                return $this->sendResponse('Reset link sent to your email.',null);
            } else {
                return $this->sendError('Unable to send reset link. Please try again.', ['error'=>'Unable to send reset link. Please try again.']);
            }

        } else {
            return $this->sendError('You are not register or invalid user');
        }
    }

    public function resetPassword(Request $request)
    {
        $input = $request->only('email', 'token', 'password', 'password_confirmation');

        $validator = Validator::make($input, [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|string|min:8|max:16|
                        regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|
                        regex:/[@$!%*?&^#^#]/'
        ], [
            'password.min' => 'The new password must be at least 8 characters.',
            'password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        $resetRecord = DB::table('password_reset_tokens')->where('email', $input['email'])->first();

        if (!$resetRecord || !Hash::check($input['token'], $resetRecord->token)) {
            return $this->sendError('Invalid or expired token.', [], 400);
        }

        $email = $resetRecord->email;
        $user =  $this->userRepositoryInterface->getByColumn(['email' => $email]);
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }

        // Check password history
        if (CommonHelper::passwordMatchesHistory($user, $request->password)) {
            return $this->sendError('Password cannot match any of the last three passwords.', [
                'error' => 'The new password cannot match any of the last three passwords.'
            ]);
        }

        $user_password = Hash::make($request->password);

        $password_history = CommonHelper::updateHistory($user->password_history, $user_password);

        $updateDetails = [
            'password_changed_at' =>Carbon::now(),
            'password' => $user_password,
            'password_history' => $password_history
        ];

        $this->userRepositoryInterface->update($user->id, $updateDetails);

        // Optionally delete the used reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return $this->sendResponse('Password has been reset successfully.', null);
    }

    private function logUserLoginActivity($user)
    {
        activity('login')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'ip' => request()->ip(),
            ])->log(__('activity_logs.user_login'));
            
    }
    
}
