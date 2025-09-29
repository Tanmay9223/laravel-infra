<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\MailTemplates;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MailTemplates::create([
            'uuid' => Str::uuid(),
            'title' => 'Forgot Password',
            'slug'  => 'forgot_password',
            'subject' => 'Reset Your {{DOMAIN}} Password',
            'body' => '<p style="text-align: center;"><strong>Trouble Logging In?</strong></p>
            <p>We received a request to reset your password for your {{DOMAIN}} account. If you made this request, click the button below to reset your password.</p>
            <p>If you didn’t request this, no action is required from your side. Your account is still secure, but we recommend you change your password immediately if you suspect any unusual activity.</p>
            <h2>Next Steps:</h2>
            <ol>
                <li>Click the button below to reset your password.</li>
                <li>Follow the instructions on the page.</li>
                <li>Set a new password for your account.</li>
            </ol>
            <h2>Security Tip:</h2>
            <p>Always use a strong password and avoid using the same one across different platforms to ensure your account remains secure.</p>
            <p style="text-align: center; margin-top: 20px;">
                <a href="{{LINK}}" style="display: inline-block; background-color: #2d3748; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                    Reset Password
                </a>
            </p><br>
            <p>Link expires in 30 minutes for your security.</p>',
        ]);

        MailTemplates::create([
            'uuid' => Str::uuid(),
            'title' => 'Email OTP verification',
            'slug'  => 'email_otp_verification',
            'subject' => 'Your {{DOMAIN}} Email Verification Code',
            'body' => '<p style="text-align: center;"><strong> Verify Your Email</strong></p>
            <p>We’ve received a request to verify your email address. Please enter the One-Time Password (OTP) below to confirm your email.</p>
            <h2>Your OTP:</h2>
            <p style="font-size: 28px; font-weight: bold; text-align: center;">{{OTP}}</p>
            <p>This code is valid for 10 minutes and can only be used once. If you did not request this verification, please disregard this email, and your account will remain unchanged.</p>
            <h2>Steps to Verify:</h2>
            <ol>
                <li>Copy the OTP above.</li>
                <li>Paste it in the verification field on our website or app.</li>
                <li>Submit the form to complete the verification process.</li>
            </ol>',
        ]);

        MailTemplates::create([
            'uuid' => Str::uuid(),
            'title' => 'New Client Registered',
            'slug'  => 'client_registered',
            'subject' => 'Registration Confirmation – Welcome to {{DOMAIN}}',
            'body' => '<p>Dear {{NAME}},</p>
            <p>Thank you for completing your registration with {{DOMAIN}}. We are excited to have you on board!</p><p>Your registration details have been successfully received and processed. If you need any assistance or have questions about the next steps, feel free to contact us at any time.</p>
            <p>We look forward to working with you and supporting your needs.</p>',
        ]);

        MailTemplates::create([
            'uuid' => Str::uuid(),
            'title' => 'New Client Created by Referral',
            'slug'  => 'client_created_by_referral',
            'subject' => 'New Referred Client Joined {{DOMAIN}}',
            'body' => '<p style="text-align: center;"><strong>Referral Success!</strong></p>
            <p>We’re excited to inform you that a new client has registered using your referral link. Congratulations! You’ll be eligible for commissions based on their activity. The more clients you refer, the more you can earn.</p>
            <h2>Client Details:</h2>
            <ul>
                <li><strong>Client Name:</strong> {{NAME}}</li>
                <li><strong>Client Email:</strong> {{EMAIL}}</li>
                <li><strong>Date:</strong> {{DATE}}</li>
            </ul>
            <h2>How It Works:</h2>
            <ul>
                <li>You earn a commission based on the activity of your referrals.</li>
                <li>Track all of your referred clients in your IB referral dashboard.</li>
                <li>Share your referral link to attract more clients and increase your earnings.</li>
            </ul>',
        ]);

        MailTemplates::create([
            'uuid'    => Str::uuid(),
            'title'   => 'Deposit Request',
            'slug'    => 'deposit_request',
            'subject' => 'Your Deposit Request is Being Processed',
            'body'    => '<p style="text-align: center;"><strong>Deposit Request Received</strong></p>
            <p>Thank you for initiating your deposit request. We’ve received your request and are currently processing it. Your deposit will be credited to your account once it has been verified and approved.</p>
            <h2>Deposit Details:</h2>
            <ul>
                <li><strong>Amount:</strong> {{CURRENCY}} {{AMOUNT}}</li>
                <li><strong>Deposit Method:</strong> {{PAYMENT_MODE}}</li>
                <li><strong>Account Number:</strong> {{ACCOUNT_NUMBER}}</li>
            </ul>
            <h2>What Happens Next:</h2>
            <ul>
                <li>Your deposit will be processed and credited to your trading account.</li>
                <li>If there’s any issue with your deposit, we’ll notify you with the reason and provide further instructions.</li>
            </ul>
            <p>If you have any questions, feel free to contact our support team. Your funds are our priority!</p>',
        ]);

        MailTemplates::create([
            'uuid'    => Str::uuid(),
            'title'   => 'Withdraw Request Submitted',
            'slug'    => 'withdraw_request',
            'subject' => 'Your Withdrawal Request Has Been Received',
            'body'    => '<p style="text-align: center;"><strong>Withdrawal Request Submitted</strong></p>
            <p>We have received your withdrawal request and are currently processing it. Please note that it may take some time for your funds to be transferred, depending on the withdrawal method and verification process.</p>
            <h2>Withdrawal Details:</h2>
            <ul>
                <li><strong>Requested Amount:</strong>{{CURRENCY}} {{AMOUNT}}</li>
                <li><strong>Withdrawal Method:</strong> {{METHOD}}</li>
                <li><strong>Account Number:</strong> {{ACCOUNT_NUMBER}}</li>
            </ul>
            <h2>What Happens Next:</h2>
            <ul>
                <li>Your withdrawal request will be reviewed and processed.</li>
                <li>If additional verification is needed, we will contact you directly.</li>
                <li>You’ll receive a confirmation once the withdrawal is successful, or an alert if there’s an issue.</li>
            </ul>
            <p>If you have any concerns or need further assistance, feel free to reach out to our support team.</p>',
        ]);

        MailTemplates::create([
            'uuid'    => Str::uuid(),
            'title'   => 'Withdraw Request Approved',
            'slug'    => 'withdraw_request_approved',
            'subject' => 'Your Withdrawal Request Has Been Approved',
            'body'    => '<p style="text-align: center;"><strong>Funds Sent to Your Account</strong></p>
            <p>We’re happy to inform you that your withdrawal request has been successfully processed and the funds have been sent to your specified withdrawal method. Please allow some time for the funds to appear in your account.</p>
            <h2>Withdrawal Details:</h2>
            <ul>
                <li><strong>Amount:</strong>{{CURRENCY}} {{AMOUNT}}</li>
                <li><strong>Withdrawal Method:</strong> {{METHOD}}</li>
                <li><strong>Account Number:</strong> {{ACCOUNT_NUMBER}}</li>
            </ul>
            <h2>What You Can Do Now:</h2>
            <ul>
                <li>Check your bank account for the transferred funds.</li>
                <li>If you have any issues or delays, contact your bank directly or reach out to us.</li>
            </ul>
            <p>If you experience any delays or need help with your transaction, don’t hesitate to contact our support team.</p>',
        ]);

        MailTemplates::create([
            'uuid'    => Str::uuid(),
            'title'   => 'Withdraw Request Reject',
            'slug'    => 'withdraw_request_reject',
            'subject' => 'Withdrawal Request Reject',
            'body'    => '<p style="text-align: center;"><strong>Withdrawal Could Not Be Processed</strong></p><p>Unfortunately, your withdrawal request could not be processed due to an issue with your account or the withdrawal method. Please find the reason for failure below and follow the suggested steps to resolve the issue.</p>
            <h2>Reason for Failure:</h2>
            <p>Insufficient funds in the trading account or incomplete withdrawal details.</p>
            <h2>Withdrawal Details:</h2>
            <ul>
                <li><strong>Requested Amount:</strong>{{CURRENCY}} {{AMOUNT}}</li>
                <li><strong>Withdrawal Method:</strong> {{METHOD}}</li>
                <li><strong>Account Number:</strong> {{ACCOUNT_NUMBER}}</li>
            </ul>
            <h2>What You Can Do Next:</h2>
            <ul>
                <li>Ensure your trading account has sufficient funds for the requested withdrawal.</li>
                <li>Check your withdrawal details for accuracy and complete any missing information.</li>
                <li>Resubmit your request once the issues are resolved.</li>
            </ul>
            <p>If you need assistance or further clarification, our support team is available to help you resolve this.</p>',
        ]);

        MailTemplates::create([
            'uuid' => Str::uuid(),
            'title' => 'Change Password',
            'slug'  => 'change_password',
            'subject' => 'Your Password Has Been Changed',
            'body' => '<p style="text-align: center;"><strong>Password Update Confirmation</strong></p>
            <p>We have successfully updated your password as requested. If you did not make this change, please contact our support team immediately to secure your account.</p>

            <h2>Next Steps:</h2>
            <ul>
                <li>Log in to your account using your new password.</li>
                <li>If you encounter any issues, use the “Forgot Password” option to reset it again.</li>
            </ul>
            <p>For your security, we recommend you update your password regularly and use a unique password for your {{DOMAIN}} account.</p>',
        ]);

        MailTemplates::create([
            'uuid' => Str::uuid(),
            'title' => 'KYC Document Submitted',
            'slug'  => 'kyc_document_submit',
            'subject' => 'KYC Documents Submitted',
            'body' => '<p style="text-align: center;"><strong>Thank You for Submitting Your KYC Documents</strong></p>
            <p>We’ve received your KYC documents, and we are currently reviewing them. Please note that it may take up to 48–72 hours to process your documents.</p>

            <h2>What Happens Next:</h2>
            <ul>
                <li>Once your documents are reviewed and verified, we will notify you of the status.</li>
                <li>If anything is missing or incorrect, we will reach out to you for further information.</li>
            </ul>
            <p>For any inquiries or issues with your KYC process, please contact our support team.</p>',
        ]);

        MailTemplates::create([
            'uuid' => Str::uuid(),
            'title' => 'EKYC Document Approved',
            'slug'  => 'ekyc_approved',
            'subject' => 'Your KYC Verification is Approved!',
            'body' => '',
        ]);

        MailTemplates::create([
            'uuid' => Str::uuid(),
            'title' => 'EKYC Document Rejected',
            'slug'  => 'ekyc_rejected',
            'subject' => 'KYC Verification Unsuccessful',
            'body' => '
                <p style="text-align: center;">
                    <strong>We regret to inform you that your KYC verification has been rejected.</strong>
                </p>
                <p>We request you to kindly upload the correct and valid documents again to proceed with your account verification.</p>
                <p>If you need help, our support team is always here to assist you.</p>
                <p>Thank you for choosing Us.</p>
                <br>
                <p>Best Regards,<br>Us Team</p>
            ',
        ]);
    }
}
