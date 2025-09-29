<?php

namespace App\Helpers\API;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Log, URL, Auth, File, Mail, Session};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Mail\{DefaultMail};
use App\Models\{
    MetaDataList
};
use Illuminate\Support\{
    Arr,Str
};
use App\Utilities\SmsGateway;

class CommonHelper
{

    const SUCCESS_STATUS = 200;
    const CREATE_SUCCESS_STATUS = 201;
    const NO_CONTENT_STATUS = 204;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const VALIDATION_ERROR_STATUS = 422;
    const PASSWORD_EXP_ERROR_STATUS = 442;

    //Directory 
    const DOCUMENTS          = 'documents/';
    const PROFILES           = 'profiles/';
    const TICKETS            = 'tickets/';


    public static function getMetaData($key)
    {
        $detail = MetaDataList::where('meta_key', $key)->select('meta_value')->first();
        return (isset($detail->meta_value)) ? $detail->meta_value : '0';
    }
    
    public static function passwordMatchesHistory($user, $newPassword)
    {
        $history = json_decode($user->password_history, true) ?? [];
        // Use array_filter to check for matches
        $matches = array_filter($history, function($password) use ($newPassword) {
            return password_verify($newPassword, $password);
        });

        return !empty($matches); // Returns true if there are matches, otherwise false
    }

    public static function updateHistory($password_history, $newPassword)
    {
        $history = json_decode($password_history, true) ?? [];
        // print_r($history); die;

        // Add the new password to the history
        array_unshift($history, $newPassword);

        // Keep only the last 3 passwords
        if (count($history) > 3) {
            array_pop($history);
        }

        return json_encode($history);
    }
    

    public static function generateUsername($firstName)
    {
        // Combine names and sanitize
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName));

        // Append random number
        $randomNumber = rand(1000, 99999);

        return $base . $randomNumber;
    }
    
    public static function generateUniqueUsername($firstName, $userRepositoryInterface)
    {
        $attempt = 0;
        $username = '';
        
        do {
            $username = self::generateUsername($firstName);
            $exists = $userRepositoryInterface->dataExists('username', $username);
            $attempt++;
        } while ($exists && $attempt < 15);

        // If still exists after 15 attempts, return empty
        if ($exists) {
            return '';
        }

        return $username;
    }



    public static function displayImage($image = null, $path = "uploads")
    {
        $defaultImage = asset('default_images/profile.png');

        if (empty($image)) {
            return $defaultImage;
        }

        $env = env('APP_ENV', config('app.env'));

        // Always prefix with 'images/' to match S3 structure
        $storagePath = 'images/' . trim($path, '/') . '/' . $image;
 
        if (in_array($env, ['production', 'staging'])) {
            if (Storage::disk('s3')->exists($storagePath)) {
                $url = Storage::disk('s3')->url($storagePath);
                return $url;
            } 
        }

        if ($env === 'local') {
            $localPath = storage_path('app/public/' . $storagePath);
            if (file_exists($localPath)) {
                return asset('storage/' . $storagePath);
            }

            $publicPath = public_path($storagePath);
            if (file_exists($publicPath)) {
                return asset($storagePath);
            }
        }
        return $defaultImage;
    }
    
    public static function uploadImage($image, $chkext = false, $destinationFolder = NULL)
    {
        $imageArray = array("png", "jpg", "jpeg", "gif", "bmp");
        $imagename = "profile.png";
        if ($image) {
            $imageext = $image->extension();
            $imgname = $image->getClientOriginalName();

            if (!in_array($imageext, $imageArray) && $chkext) {
                return "";
            }
            $mimeType = $image->getMimeType();
            if (!in_array($mimeType, ['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/bmp'])) {
                return "";
            }

            $imagename = rand(100, 999) . '_' . time() . '.' . $imageext;

            if(env('APP_ENV') == 'local'){
                $folderPath = 'images/' . $destinationFolder;
                $image->storeAs($folderPath, $imagename, 'public');
            }else if(env('APP_ENV') == 'production'){
                $imagename = 'profile.png';
            }
        }
        return  $imagename;
    }


    public static function getRandString($strength = 5)
    {
        $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        return strtoupper($random_string);
    }

    public static function sendMail($to, $subject, $greeting, $body, $attachment = null)
    {
        $response = false;

        Log::channel('emaillog')->info("Preparing to send email to {$to} with subject '{$subject}'");

        try {
            if (!empty($to) && !empty($subject) && !empty($greeting) && !empty($body)) {
                Log::channel('emaillog')->info("Valid inputs for email to {$to}");

                // Send the email
                Mail::to($to)->send(new DefaultMail(
                    ucfirst(strtolower($subject)),
                    $greeting,
                    $body,
                    $attachment
                ));

                Log::channel('emaillog')->info("Email successfully sent to {$to}");
                $response = true;
            } else {
                Log::channel('emaillog')->error("Invalid email parameters for {$to}");
            }
        } catch (Exception $e) {
            // Log the exception message
            Log::channel('emaillog')->error("Failed to send email to {$to}: " . $e->getMessage());
        }

        Log::channel('emaillog')->info("Returning response for email to {$to}");
        return $response;
    }

    public static function sendOTP($type, $user, $mailTemplatesRepositoryInterface)
    {
        $otp = self::generateRandomOTP();
        $recipient = $user->email;

        if($type == 'email'){

            $mail_content = $mailTemplatesRepositoryInterface->getByColumn(['status' => 1, 'slug' => 'email_otp_verification']);

            if($mail_content)
            {
                $greeting = $user->first_name. ' '.$user->last_name;
                $domain = CommonHelper::getMetaData('site_title');
                $domain_support_email = CommonHelper::getMetaData('support_email');
                $content = $mail_content->body;
                $subject = $mail_content->subject;

                $formattedSubject = str_replace(
                    ['{{DOMAIN}}'],
                    [$domain],
                    $subject
                );

                $formattedContent = str_replace(
                    ['{{OTP}}','{{DOMAIN}}'],
                    [$otp,$domain],
                    $content
                );
                CommonHelper::sendMail($recipient, $formattedSubject, $greeting, $formattedContent);
            }

        }else if($type == 'mobile'){
            $message = 'Your OTP for mobile verification is '.$otp.'. Do not share this code. Valid for 10 minutes.';

            SmsGateway::send($recipient, $message);
        }

        return $otp;
    }

    public static function generateRandomOTP(){
        $otp = rand(100000, 999999);
        //$otp = '123456';
        return $otp;
    }

}