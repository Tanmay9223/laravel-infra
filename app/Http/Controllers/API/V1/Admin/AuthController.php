<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Passport\{
    HasApiTokens, Token, RefreshToken
};
use Illuminate\Support\Facades\{Auth, Hash, Validator, Password};
use App\Classes\ApiResponseClass;
use App\Helpers\API\CommonHelper;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Interfaces\Admin\{
    AdminRepositoryInterface,RoleRepositoryInterface
};


class AuthController extends BaseController
{
    public $successStatus        =  CommonHelper::SUCCESS_STATUS;
    public $createSuccessStatus  =  CommonHelper::CREATE_SUCCESS_STATUS;
    public $noContentStatus      =  CommonHelper::NO_CONTENT_STATUS;
    public $unauthorizedStatus   =  CommonHelper::UNAUTHORIZED;
    public $forbiddenStatus      =  CommonHelper::FORBIDDEN;

    private AdminRepositoryInterface $adminRepositoryInterface;
    private RoleRepositoryInterface  $roleRepositoryInterface;
    

    public function __construct(AdminRepositoryInterface $adminRepositoryInterface,RoleRepositoryInterface  $roleRepositoryInterface)
    {
        $this->adminRepositoryInterface = $adminRepositoryInterface;
        $this->roleRepositoryInterface = $roleRepositoryInterface;
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
        $data['footer_text'] = "<p>Risk statement: An investment in derivatives may mean investors may lose an amount even greater than their original investment. Anyone wishing to invest in any of the products mentioned in ".env('APP_URL'). " should seek their own financial or professional advice. Trading of securities, forex, stock market, commodities, options and futures may not be suitable for everyone and involves the risk of losing part or all of your money. Trading in the financial markets has large potential rewards, but also large potential risk. You must be aware of the risks and be willing to accept them in order to invest in the markets. Don't invest and trade with money which you can't afford to lose. Forex Trading are not allowed in some countries, before investing your money, make sure whether your country is allowing this or not.</p><p>The website is operated by".env('APP_NAME')." Ltd; ".env('APP_NAME')." Ltd is registered under Republic of Mauritius as a Mauritius Authorized Company with registration number 111111. ".env('APP_NAME')." Ltd under the name of ".env('APP_NAME')." Global Limited is authorized and regulated in Mauritius by the Financial Services Commission (FSC), License Number: GB111111. ".env('APP_NAME')." Global Limited is registered under Republic of Mauritius with company number: 11111</p>";
        $data['other_links'] = $other_links;

        return ApiResponseClass::sendResponseCode($data, $this->successStatus, __('custom_messages.initialize_data'),);
    }

    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError(__('custom_messages.validation_error'), $validator->errors(), $this->validationErrorStatus);
        }

        $admin =  $this->adminRepositoryInterface->getByColumn(['email' => $request->email]);

        if(!empty($admin)) {
            if($admin->status){
                $roleStatus = $this->roleRepositoryInterface->getByColumn(['id' => $admin->role_id,'status' => 1]);
                if(!empty($roleStatus)) {
                    if(Hash::check($request->password, $admin->password)){
                        $success = [
                            'name' => $admin->name,
                            'email' => $admin->email,
                            'token' => $admin->createToken('Admin',['admin'])->accessToken,
                        ];

                        return $this->sendResponse(__('custom_messages.login_successfully'), $success);
                    }else{
                        return $this->sendError(__('custom_messages.password_invalid'));
                    }
                }else{
                    return $this->sendError(__('custom_messages.inactive_role_error'));
                }
            } else {
                return $this->sendError(__('custom_messages.inactive_user_error'));
            }
        } else {
            return $this->sendError(__('custom_messages.not_registered'));
        }
    }

    public function forgotPassword(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
            return $this->sendError(__('custom_messages.validation_error'), $validator->errors(), $this->validationErrorStatus);
        }

        try {
            $exists = $this->adminRepositoryInterface->dataExists('email', $input['email']);
            if(!$exists){
                return $this->sendError(__('custom_messages.not_registered'));
            }

            $response = Password::broker('admins')->sendResetLink($input);
            if ($response == Password::RESET_LINK_SENT) {
                return $this->sendResponse(__('custom_messages.reset_link_sent_success'));
            } else {
                return $this->sendError(__('custom_messages.reset_link_sent_error'), ['error' => 'Unable to send reset link. Please try again.']);
            }

        } catch (ModelNotFoundException $e) {
            return $this->sendError(__('custom_messages.not_registered'));
        }
    }

    public function resetPassword(Request $request)
    {
        $input = $request->only('email', 'token', 'password', 'password_confirmation');
        $validator = Validator::make($input, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*?&^#]/|max:16'
        ],[
            'password.min' => 'The new password must be at least 8 characters.',
            'password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if($validator->fails()){
            return $this->sendError(__('custom_messages.validation_error'), $validator->errors(), $this->validationErrorStatus);
        }

        try {
            $admin = $this->adminRepositoryInterface->getByColumn(['email' => $input['email']]);
            if(!$admin){
                return $this->sendError(__('custom_messages.not_registered'));
            }
            
            $newPassword = Hash::make($request->password);
            $response = Password::broker('admins')->reset($input, function ($user, $newPassword) use($admin, $request) {
                if (CommonHelper::passwordMatchesHistory($admin, $request->password)) {
                    return $this->sendError(__('custom_messages.password_match_error'), ['error' => 'The new password cannot match any of the last three passwords.']);
                }

                $user->password = $newPassword;
                $user->password_changed_at = Carbon::now();
                $user->save();
            });

            if ($response === Password::PASSWORD_RESET) {
                $password_history = CommonHelper::updateHistory($admin->password_history, $newPassword);

                $updateDetails = [
                    'password_history' => $password_history
                ];

                $this->adminRepositoryInterface->update($admin->id, $updateDetails);

                return $this->sendResponse(__('custom_messages.password_reset_success'));
            } else {
                return $this->sendError(__('custom_messages.password_reset_error'), ['error' => 'Failed to reset password. Please try again.']);
            }

        } catch (ModelNotFoundException $e) {
            return $this->sendError(__('custom_messages.not_registered'));
        }
    }
    
}
