<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\{JsonResponse, Request};
use App\Classes\ApiResponseClass;
use App\Helpers\API\CommonHelper;
use Illuminate\Support\Facades\{DB,Session, Auth, Hash, Validator};
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use App\Interfaces\Admin\{
    AdminRepositoryInterface
};
use App\Interfaces\{
    MailTemplatesRepositoryInterface
};
use App\Http\Resources\Admin\{
    ProfileResource
};

class ProfileController extends BaseController
{    

    private AdminRepositoryInterface $adminRepositoryInterface;   
    private MailTemplatesRepositoryInterface $mailTemplatesRepositoryInterface;   
     

    public function __construct(AdminRepositoryInterface $adminRepositoryInterface,MailTemplatesRepositoryInterface $mailTemplatesRepositoryInterface)
    {
        $this->adminRepositoryInterface = $adminRepositoryInterface;
        $this->mailTemplatesRepositoryInterface = $mailTemplatesRepositoryInterface;
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
        ]);
        
        if($validator->fails()){
            return ApiResponseClass::sendErrorCode(__('custom_messages.validation_error'),$this->validationErrorStatus, $validator->errors());
        }

        $admin = Auth::user();
        if (!Hash::check($request->current_password, $admin->password)) {
            return ApiResponseClass::sendErrorCode(__('custom_messages.current_password_error'), 200, ['error' => __('custom_messages.current_password_error')]);
        }

        $validator = Validator::make($request->all(), [
            'new_password'     => ['required', 'string', 'min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/','regex:/[@$!%*?&^#]/','max:16','confirmed']
        ],[
            'new_password.min' => 'The new password must be at least 8 characters.',
            'new_password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if($validator->fails()){
            return ApiResponseClass::sendErrorCode(__('custom_messages.validation_error'), $this->validationErrorStatus, $validator->errors());
        }

        // Check if the new password matches the last three passwords
        if (CommonHelper::passwordMatchesHistory($admin, $request->new_password)) {
            return ApiResponseClass::sendErrorCode(__('custom_messages.password_match_error'), 200, ['error' => __('custom_messages.password_match_error')]);
        }
        $newPassword = Hash::make($request->new_password);
        
        $password_history = CommonHelper::updateHistory($admin->password_history, $newPassword);

        $updateDetails = [
            'password_changed_at' => Carbon::now(),
            'password' => $newPassword,
            'password_history' => $password_history
        ];

        $this->adminRepositoryInterface->update($admin->id, $updateDetails);

        $mail_content = $this->mailTemplatesRepositoryInterface->getByColumn(['status' => 1,'slug' => 'change_password']);
        if($mail_content)
        {
            $email = $admin->email;
            $greeting = $name = $admin->name;
            $domain_ltd =  CommonHelper::getMetaData('site_name');
            $content = $mail_content->body;
            $subject = $mail_content->subject;
            $href = config('app.frontend_url').'/login';

            $formattedContent = str_replace(
                ['{{NAME}}','{{DOMAIN}}'],
                [$name,$domain_ltd],
                $content
            );
            CommonHelper::sendMail($email, $subject, $greeting, $formattedContent);
        }
        return $this->sendResponse( 'Password changed successfully.',$this->noContentStatus);

    }

    public function profile(): JsonResponse
    {
        $admin_data = Auth::user();
        if(!empty($admin_data)){

            $avatar = $admin_data->avatar ?? null;
            $admin_data->avatar = CommonHelper::displayImage($avatar, CommonHelper::PROFILES);
            $role = $admin_data->role_id;
            if ($role) {
                $admin_data->sidebarMenus = CommonHelper::getAllPermissions($role);
            }

            return ApiResponseClass::sendResponseCode(new ProfileResource($admin_data),  $this->successStatus,__('custom_messages.profile_found'));
        }
        return ApiResponseClass::sendResponseCode(__('custom_messages.profile_not_found'), $this->successStatus);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $token = Auth::user()->token();
            $token->revoke();
            return $this->sendResponse(__('custom_messages.logout_successfully'));
        }
        else{
            return $this->sendError(__('custom_messages.unauthorized'), ['error'=>'Unauthorized'], $this->unauthorizedStatus);
        }
    }


}
