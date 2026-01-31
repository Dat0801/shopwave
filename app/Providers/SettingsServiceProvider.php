<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            $settings = Setting::all();

            foreach ($settings as $setting) {
                // Map DB settings to config keys
                if (str_starts_with($setting->key, 'mail_')) {
                    $this->overrideMailConfig($setting->key, $setting->value);
                }
                // Add other overrides here (e.g. app.name, etc.)
                if ($setting->key === 'site_name') {
                    Config::set('app.name', $setting->value);
                }
                if ($setting->key === 'contact_email') {
                    Config::set('app.contact_email', $setting->value);
                }
                if ($setting->key === 'contact_phone') {
                    Config::set('app.contact_phone', $setting->value);
                }
                if ($setting->key === 'address') {
                    Config::set('app.contact_address', $setting->value);
                }
                if ($setting->key === 'timezone') {
                    Config::set('app.timezone', $setting->value);
                    date_default_timezone_set($setting->value);
                }
                if ($setting->key === 'locale') {
                    Config::set('app.locale', $setting->value);
                    app()->setLocale($setting->value);
                }
                if ($setting->key === 'currency') {
                    Config::set('app.currency', $setting->value);
                }
                if ($setting->key === 'site_logo') {
                    Config::set('app.logo', $setting->value);
                }
                if ($setting->key === 'site_favicon') {
                    Config::set('app.favicon', $setting->value);
                }
            }
        }
    }

    private function overrideMailConfig($key, $value)
    {
        // Map db keys (mail_host) to config keys (mail.mailers.smtp.host)
        // Assuming we are overriding the default SMTP mailer or the one specified in mail_mailer
        
        switch ($key) {
            case 'mail_mailer':
                Config::set('mail.default', $value);
                break;
            case 'mail_host':
                Config::set('mail.mailers.smtp.host', $value);
                break;
            case 'mail_port':
                Config::set('mail.mailers.smtp.port', $value);
                break;
            case 'mail_username':
                Config::set('mail.mailers.smtp.username', $value);
                break;
            case 'mail_password':
                Config::set('mail.mailers.smtp.password', $value);
                break;
            case 'mail_encryption':
                Config::set('mail.mailers.smtp.encryption', $value);
                break;
            case 'mail_from_address':
                Config::set('mail.from.address', $value);
                break;
            case 'mail_from_name':
                Config::set('mail.from.name', $value);
                break;
        }
    }
}
