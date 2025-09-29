<?php

namespace App\Helpers\API;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Log, URL, Auth, File, Mail, Session, Storage, Hash, Crypt};
use App\Mail\{DefaultMail};
use Illuminate\Support\{
    Arr,Str
};

class BasicHelper
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
    const DEMO = '0';
    const LIVE = '1';

    const INCRYPTION_KEY = "SkYt0iR-8213Gt1!!";

    public static function encryptData($plainText)
    {
        try {
            $secretKey = self::INCRYPTION_KEY; 
            $key = hex2bin(hash('sha256', $secretKey)); // 32-byte key

            $iv = random_bytes(16); // 16-byte IV for AES-256-CBC

            $encrypted = openssl_encrypt(
                $plainText,
                'AES-256-CBC',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($encrypted === false) {
                return null;
            }

            $encryptedBase64 = str_replace(['+', '/'], ['-', '_'], base64_encode($encrypted));
            $ivHex = bin2hex($iv);

            return $ivHex . '::' . $encryptedBase64;
        } catch (Exception $e) {
            error_log('Encryption Error: ' . $e->getMessage());
            return null;
        }
    }

    public static function decryptData($encrypted) {
        try {

            $secretKey = self::INCRYPTION_KEY; //same constant as used in encryptData
            $key = hex2bin(hash('sha256', $secretKey));

            // Split IV and Ciphertext
            $parts = explode("::", $encrypted);
            if (count($parts) !== 2) {
                //echo "Invalid encrypted data format";
                return null;
            }

            $iv = hex2bin($parts[0]);
            $encryptedText = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));

            if (strlen($iv) !== 16) {
                return null;
            }

            // Decrypt
            $decrypted = openssl_decrypt(
                $encryptedText,
                'AES-256-CBC',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($decrypted === false) {
                return null;
            }

            return $decrypted;
        } catch (Exception $e) {
            error_log('Decryption Error: ' . $e->getMessage());
            return null;
        }
    }
    
    public static function sendEmailOtp($user, $kycRepositoryInterface, $mailTemplatesRepositoryInterface)
    {
        $now = Carbon::now();
        $details = $kycRepositoryInterface->getByColumn(['user_id' => $user->id]);
        $email_validity_time = CommonHelper::getMetaData('email_validity'); // in minutes

        if ($details) {
            $no_of_attempts = $details->no_of_attempts ?? 0;
            $block_time = $details->block_time;
            $email_validity = $details->email_validity;
            $email_attempt_limit = CommonHelper::getMetaData('email_attempt');

            // Step 1: Check if OTP is already valid
            if (!empty($email_validity)) {
                $validUntil = Carbon::parse($email_validity);

                if ($now->lessThanOrEqualTo($validUntil)) {
                    $difference_in_minutes = $now->diffInMinutes($validUntil);

                    if ($difference_in_minutes <= $email_validity_time) {
                        return [
                            'status' => false,
                            'message' => 'OTP already sent on email and is still valid.'
                        ];
                    }
                }
            }

            // Step 2: Check block time
            if (!empty($block_time)) {
                $blockTime = Carbon::parse($block_time);

                if ($now->lessThan($blockTime)) {
                    $remainingMinutes = $now->diffInMinutes($blockTime);
                    return [
                        'status' => false,
                        'message' => "You have reached the maximum number of attempts. Please try again after {$remainingMinutes} minutes."
                    ];
                }
            } else {
                // Step 3: Check attempt limit only if there's no block time
                if ($no_of_attempts >= $email_attempt_limit) {
                    $newBlockTime = $now->copy()->addMinutes($email_validity_time);
                    $kycRepositoryInterface->updateByColumn(['user_id' => $user->id], ['block_time' => $newBlockTime]);
                    return [
                        'status' => false,
                        'message' => "You have reached the maximum number of attempts. Please try again after {$email_validity_time} minutes."
                    ];
                }
            }

            $otp = CommonHelper::sendOTP('email', $user, $mailTemplatesRepositoryInterface);

            $updateData = [
                'email_otp' => $otp,
                'email_validity' => $now->addMinutes($email_validity_time),
                'no_of_attempts' => 0,
                'block_time' => null // Reset block time on successful send
            ];

            $kycRepositoryInterface->updateByColumn(['user_id' => $user->id], $updateData);
        } else {
            $otp = CommonHelper::sendOTP('email', $user, $mailTemplatesRepositoryInterface);

            $storeData = [
                'user_id' => $user->id,
                'uuid' => Str::uuid(),
                'email_otp' => $otp,
                'email_validity' => $now->addMinutes($email_validity_time),
                'no_of_attempts' => 0,
            ];

            $kycRepositoryInterface->store($storeData);
        }

        return ['status' => true, 'message' => 'OTP sent successfully.'];
    }
}