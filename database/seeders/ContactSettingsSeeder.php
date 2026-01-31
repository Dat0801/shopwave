<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ContactSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'contact_email', 'value' => 'support@shopwave.com', 'group' => 'general', 'type' => 'email'],
            ['key' => 'contact_phone', 'value' => '+1 (555) 123-4567', 'group' => 'general', 'type' => 'text'],
            ['key' => 'address', 'value' => '123 Shop Street, Commerce City, CA 90210', 'group' => 'general', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
