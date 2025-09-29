<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{MetaDataList};
use Illuminate\Support\Facades\{DB, Log, Schema};

class MetaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($admin = []): void
    {
            $meta_data_list = [
                [
                    'group_id' => 101,
                    'meta_title'=>'Site Name',
                    'meta_key'=>'site_name',
                    'meta_value' => env('APP_NAME'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Site Title',
                    'meta_key'=>'site_title',
                    'meta_value' => env('APP_NAME'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Login Title',
                    'meta_key'=>'login_title',
                    'meta_value' => env('APP_NAME'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'App Name',
                    'meta_key'=>'app_name',
                    'meta_value' => env('APP_NAME'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'App Tagline',
                    'meta_key'=>'app_tagline',
                    'meta_value' => '',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Site Author',
                    'meta_key'=>'site_author',
                    'meta_value' => env('APP_NAME'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Site Keywords',
                    'meta_key'=>'site_keywords',
                    'meta_value' => env('APP_NAME'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Site Description',
                    'meta_key'=>'site_description',
                    'meta_value' => env('MAIL_FROM_ADDRESS'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Disclaimer Statement',
                    'meta_key'=>'disclaimer_statement',
                    'meta_value' => '',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Website URL',
                    'meta_key'=>'website_url',
                    'meta_value' => env('APP_URL'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Terms and Conditions (Client Portal)',
                    'meta_key'=>'terms_condition_link_client',
                    'meta_value' => env('APP_URL').'/terms-of-business/',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Terms and Conditions (Partner Portal)',
                    'meta_key'=>'terms_condition_partner',
                    'meta_value' => env('APP_URL').'/terms-of-business/',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'About Us',
                    'meta_key'=>'about_us',
                    'meta_value' => env('APP_URL').'/about-us/',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Privacy Policy',
                    'meta_key'=>'privacy_policy_link',
                    'meta_value' => env('APP_URL').'/privacy-policy/',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Facebook',
                    'meta_key'=>'facebook_link',
                    'meta_value' => '#',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Linkedin',
                    'meta_key'=>'linkedin_link',
                    'meta_value' => '#',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Youtube',
                    'meta_key'=>'youtube_link',
                    'meta_value' => '#',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Instagram',
                    'meta_key'=>'instagram_link',
                    'meta_value' => '#',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Support Email',
                    'meta_key'=>'support_email',
                    'meta_value' => env('MAIL_FROM_ADDRESS'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Support Name',
                    'meta_key'=>'support_name',
                    'meta_value' => env('APP_NAME'),
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Auto Close Ticket',
                    'meta_key'=>'auto_close_ticket',
                    'meta_value' => '',
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Need to Verify Mail',
                    'meta_key'=>'require_email_verification',
                    'meta_value' => true,
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Mail Validity Time',
                    'meta_key'=>'email_validity',
                    'meta_value' => 10,
                    'status' => true,
                ],
                [
                    'group_id' => 101,
                    'meta_title'=>'Mail Verify Attempts',
                    'meta_key'=>'email_attempt',
                    'meta_value' => 5,
                    'status' => true,
                ],
                
                                
            ];

            try {
                if(Schema::hasTable('tab_parameters') && Schema::hasTable('meta_data_lists')){
                    foreach ($meta_data_list as $key => $value) {
                        $this->metaData( $value);
                    }
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
    }

    private function metaData($data) {
        $metadata = MetaDataList::create($data);
        return $metadata;
    }
}
