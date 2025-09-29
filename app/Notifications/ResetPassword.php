<?php

namespace App\Notifications;

use App\Helpers\API\CommonHelper;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param string $token
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $frontendUrl = config('app.frontend_url');
        // $resetUrl = "{$frontendUrl}/reset-password?token={$this->token}&email={$notifiable->getEmailForPasswordReset()}";
        $resetUrl = "{$frontendUrl}/reset-password?token={$this->token}";


        // $body = "<p>You are receiving this email because we received a password reset request for your account.</p>
        //         <a href='". $resetUrl ."' style='display: inline-block; background-color: #2d3748; color: white; padding: 10px 20px; text-align: center; text-decoration: none; border-radius: 5px;'>Reset Password</a>
        //         <p>This password reset link will expire in 60 minutes.</p>
        //         <p>If you did not request a password reset, no further action is required.</p>
        //         <p>Thank you for choosing MLM</p>";

        $domain_ltd = CommonHelper::getMetaData('site_name');
        $body ="<p style='text-align: center;'><strong>Trouble Logging In?</strong></p>
            <p>We received a request to reset your password for your {$domain_ltd} account. If you made this request, click the button below to reset your password.</p>
            <p>If you didn’t request this, no action is required from your side. Your account is still secure, but we recommend you change your password immediately if you suspect any unusual activity.</p>
            <h2>Next Steps:</h2>
            <ol>
                <li>Click the button below to reset your password.</li>
                <li>Follow the instructions on the page.</li>
                <li>Set a new password for your account.</li>
            </ol>
            <h2>Security Tip:</h2>
            <p>Always use a strong password and avoid using the same one across different platforms to ensure your account remains secure.</p>
            <p style='text-align: center; margin-top: 20px;'>
                <a href='{$resetUrl}' style='display: inline-block; background-color: #2d3748; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;'>
                    Reset Password
                </a>
            </p><br>
            <p>Link expires in 30 minutes for your security.</p>";

        $subject = "Reset Your {$domain_ltd} Password";

        $body = ['body'=>$body];

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.defaultemail',$body);



    }
}

