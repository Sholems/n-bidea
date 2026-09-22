<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'NB-CCI Portal'],
            ['key' => 'portal_description', 'value' => 'Nigerian Business Chamber of Commerce and Industry - Business Enumeration & Verification Portal'],
            ['key' => 'verification_validity_months', 'value' => '12'],
            ['key' => 'certification_fee', 'value' => '25000'],
            ['key' => 'renewal_fee', 'value' => '15000'],
            ['key' => 'max_file_size_mb', 'value' => '5'],
            ['key' => 'allowed_file_types', 'value' => 'pdf,jpg,jpeg,png'],
            ['key' => 'contact_email', 'value' => 'info@nb-cci.gov.ng'],
            ['key' => 'contact_phone', 'value' => '+234-800-NB-CCI'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
