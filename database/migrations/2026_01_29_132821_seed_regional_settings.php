<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            ['key' => 'currency', 'value' => 'USD', 'group' => 'regional', 'type' => 'text', 'label' => 'Currency'],
            ['key' => 'timezone', 'value' => 'UTC', 'group' => 'regional', 'type' => 'text', 'label' => 'Timezone'],
            ['key' => 'locale', 'value' => 'en', 'group' => 'regional', 'type' => 'text', 'label' => 'Language'],
        ];

        foreach ($settings as $setting) {
            $exists = DB::table('settings')->where('key', $setting['key'])->first();
            
            if ($exists) {
                // If it exists but might have invalid values (e.g. from previous manual saves), reset to safe defaults
                // Checking for spaces or parens is a heuristic for "English (US)" or "(GMT...)"
                if ($exists->value && (str_contains($exists->value, ' ') || str_contains($exists->value, '('))) {
                    DB::table('settings')->where('key', $setting['key'])->update(['value' => $setting['value']]);
                }
            } else {
                DB::table('settings')->insert(array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't necessarily want to delete them as they might have been modified by user
    }
};
