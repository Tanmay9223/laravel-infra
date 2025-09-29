<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\{ JsonResponse , Request};
use Illuminate\Support\Facades\{DB, Validator, Auth, Hash};
use App\Classes\ApiResponseClass;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FA\Support\QRCode;
use App\Support\Google2FAAuthentication;
use App\Models\{
    Admin, User, Google2faDetail
};

class Google2FAController extends BaseController
{

    public function get2FADetails()
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $kyc = DB::table('kyc_details')
            ->where('user_id', $user->id)
            ->select('email_verification_status')
            ->first();

            // if (!$kyc || $kyc->email_verification_status != 1) {
            //     DB::rollBack();
            //     return $this->sendError('Email not verified. Please verify your email before using 2FA.', 'Email not verified.');
            // }

            // Dynamically fetch the user or admin based on the authenticated user
            $model = $user instanceof Admin
                ? Admin::where('id', $user->id)->with('google2faKey')->first()
                : User::where('id', $user->id)->with('google2faKey')->first();

            // Check if user or admin exists
            if (!$model) {
                DB::rollBack();
                return $this->sendError('User not found.', 'Error in fetching 2FA details.');
            }

            // If 2FA is enabled, get the key, otherwise generate a new one
            if ($model->google2faKey) {
                $data['key'] = $model->google2faKey->google_2fa_key;
            } else {
                $google2fa = new Google2FA();
                $data['key'] = $google2fa->generateSecretKey();
            }

            $emailVerified = DB::table('kyc_details')
            ->where('user_id', $user->id)
            ->value('email_verification_status');
        
        $data['email_verification_status'] = $emailVerified ?? 0; // default to 0 if null            

            DB::commit();
            return ApiResponseClass::sendResponse($data, $this->successStatus, 'Details fetched successfully.');

        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::rollback($ex);
        }
    }

    // public function enable2FA(Request $request)
    // {
    //     DB::beginTransaction();

    //     $validator = Validator::make($request->all(), [
    //         'key' => 'required',
    //         'otp' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
    //     }

    //     try {
    //         $user = Auth::user(); // Works for both admin and user
    //         $google2Fa = new Google2FA();

    //         $key = $request->input('key');
    //         $code = $request->input('otp');

    //         if (!$google2Fa->verifyKey($key, $code, 1)) {
    //             DB::rollBack();
    //             return $this->sendError('Invalid OTP. Please try again.', 'Invalid OTP.');
    //         }

    //         // Determine if user is admin or regular user
    //         if ($user instanceof Admin) {
    //             $user->google2faKey()->create(['google_2fa_key' => $key]);
    //             $user->update(['is_google2fa_enable' => 1]);
    //         } elseif ($user instanceof User) {
    //             $user->google2faKey()->create(['google_2fa_key' => $key]);
    //             $user->update(['is_google2fa_enable' => 1]);
    //         } else {
    //             DB::rollBack();
    //             return $this->sendError('User type not recognized.', 'Failed to enable 2FA.');
    //         }

    //         DB::commit();

    //         return ApiResponseClass::sendResponse(null, '2FA enabled successfully.', $this->successStatus);

    //     } catch (\Exception $ex) {
    //         DB::rollBack();
    //         return ApiResponseClass::rollback($ex);
    //     }
    // }

    public function enable2FA(Request $request)
    {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), [], $this->validationErrorStatus);
        }

        try {
            $user = Auth::user(); // Works for both admin and user
            $google2Fa = new Google2FA();

            $key = $request->input('key');
            $code = $request->input('otp');

            if (!$google2Fa->verifyKey($key, $code, 1)) {
                DB::rollBack();
                return $this->sendError('Invalid OTP. Please try again.', 'Invalid OTP.');
            }

            // Determine if user is admin or regular user
            if ($user instanceof Admin) {
                $user->google2faKey()->create(['google_2fa_key' => $key]);
                $user->update(['is_google2fa_enable' => 1]);
                DB::table('oauth_access_tokens')
                ->where('user_id', $user->id)
                ->update(['revoked' => true]);

            // Revoke associated refresh tokens
            DB::table('oauth_refresh_tokens')
                ->whereIn('access_token_id', function ($query) use ($user) {
                    $query->select('id')
                          ->from('oauth_access_tokens')
                          ->where('user_id', $user->id);
                })
                ->update(['revoked' => true]);


            } elseif ($user instanceof User) {
                $user->google2faKey()->create(['google_2fa_key' => $key]);
                $user->update(['is_google2fa_enable' => 1]);
                DB::table('oauth_access_tokens')
                ->where('user_id', $user->id)
                ->update(['revoked' => true]);

            // Revoke associated refresh tokens
            DB::table('oauth_refresh_tokens')
                ->whereIn('access_token_id', function ($query) use ($user) {
                    $query->select('id')
                          ->from('oauth_access_tokens')
                          ->where('user_id', $user->id);
                })
                ->update(['revoked' => true]);
            } else {
                DB::rollBack();
                return $this->sendError('User type not recognized.', 'Failed to enable 2FA.');
            }

            DB::commit();

            activity('2FA_enable')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'ip' => request()->ip(),
                // 'document_type' => $request->type,
            ])
            ->log(__('activity_logs.2FA_enable'));

            return ApiResponseClass::sendResponse(null, $this->successStatus,'2FA enabled successfully.');

        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::rollback($ex);
        }
    }

    public function verify2FA(Request $request)
    {
    DB::beginTransaction();

    $validator = Validator::make($request->all(), [
        'otp' => 'required',
    ]);

    if ($validator->fails()) {
        return $this->sendError('Validation Error.', $validator->errors());
    }

    try {
        $user = Auth::user();
        $google2Fa = new Google2FA();
        $code = $request->input('otp');

        // Check if the user has a 2FA key
        if (!$user->google2faKey) {
            DB::rollBack();
            return $this->sendError('2FA key not found. Please enable 2FA first.', '2FA key not found.');
        }

        // Verify the OTP against the user's 2FA key
        $valid = $google2Fa->verifyKey($user->google2faKey->google_2fa_key, $code);

        if ($valid) {
            // Update the 2FA status for the authenticated user
            $user->update(['is_google2fa_enable' => 1]);

            DB::commit();
            return ApiResponseClass::sendResponse(null, $this->successStatus,'2FA verified successfully.');
        } else {
            DB::rollBack();
            return $this->sendError('Invalid OTP. Please try again.', 'Invalid OTP.');
        }
    } catch (\Exception $ex) {
        DB::rollBack();
        return ApiResponseClass::rollback($ex);
    }
    }

    // public function disable2FA(Request $request)
    // {
    //     DB::beginTransaction();

    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'current_password' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Validation Error.', $validator->errors());
    //     }

    //     $user = Auth::user();

    //     // Verify the current password
    //     if (!Hash::check($request->current_password, $user->password)) {
    //         return $this->sendError('Current password is incorrect.', ['error' => 'Current password is incorrect.']);
    //     }

    //     try {
    //         // Determine if the user is an Admin or User
    //         $model = $user instanceof Admin ? Admin::find($user->id) : User::find($user->id);

    //         // Ensure the user exists
    //         if (!$model) {
    //             DB::rollBack();
    //             return $this->sendError('User not found.', 'Error in 2FA disable. Please try again.');
    //         }

    //         // Delete the 2FA key and disable 2FA
    //         $model->google2faKey()->delete();
    //         $model->update(['is_google2fa_enable' => 0]);

    //         DB::commit();
    //         return ApiResponseClass::sendResponse(null, '2FA disabled successfully.', $this->successStatus);

    //     } catch (\Exception $ex) {
    //         DB::rollBack();
    //         return ApiResponseClass::rollback($ex);
    //     }
    // }

    public function disable2FA(Request $request)
    {
        DB::beginTransaction();
    
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
    
        $user = Auth::user();
    
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->sendError('Current password is incorrect.', ['error' => 'Current password is incorrect.']);
        }
    
        try {
            // Determine user model (Admin or User)
            $model = $user instanceof Admin ? Admin::find($user->id) : User::find($user->id);
    
            if (!$model) {
                DB::rollBack();
                return $this->sendError('User not found.', 'Error in 2FA disable. Please try again.');
            }
    
            // Delete the 2FA key and update flag
            $model->google2faKey()->delete();
            $model->update(['is_google2fa_enable' => 0]);
    
            // Determine token type (optional if you're using Laravel Passport only)
            $tokenType = $user instanceof Admin ? Admin::class : User::class;
    
            // Revoke access tokens
            DB::table('oauth_access_tokens')
                ->where('user_id', $user->id)
                // ->where('tokenable_type', $tokenType) // Uncomment if using Sanctum with polymorphic tokens
                ->update(['revoked' => true]);
    
            // Revoke associated refresh tokens
            DB::table('oauth_refresh_tokens')
                ->whereIn('access_token_id', function ($query) use ($user /*, $tokenType*/) {
                    $query->select('id')
                          ->from('oauth_access_tokens')
                          ->where('user_id', $user->id);
                          // ->where('tokenable_type', $tokenType); // Uncomment if using Sanctum
                })
                ->update(['revoked' => true]);
    
            DB::commit();

            activity('2FA_disable')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'ip' => request()->ip(),
                // 'document_type' => $request->type,
            ])
            ->log(__('activity_logs.2FA_disable'));

            return ApiResponseClass::sendResponse(null, $this->successStatus,'2FA disabled successfully.');
    
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::rollback($ex);
        }
    }
    
    
}
