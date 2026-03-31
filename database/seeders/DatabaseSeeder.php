<?php

namespace Database\Seeders;

use App\Models\AdPreset;
use App\Models\CountryRate;
use App\Models\User;
use App\Models\PublisherProfile;
use App\Models\ClickDivider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@installsbank.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123456'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Demo publisher
        $publisher = User::firstOrCreate(
            ['email' => 'publisher@demo.com'],
            [
                'name' => 'Demo Publisher',
                'password' => Hash::make('demo123456'),
                'role' => 'publisher',
                'status' => 'active',
                'website' => 'https://demo.com',
            ]
        );
        PublisherProfile::firstOrCreate(['user_id' => $publisher->id], ['balance' => 12.5, 'total_earnings' => 45.00, 'payment_enabled' => true, 'test_status' => 'completed', 'test_total_clicks' => 5200, 'contract_type' => 'per_click']);
        ClickDivider::firstOrCreate(['user_id' => $publisher->id], ['divider_value' => 1, 'is_enabled' => false]);

        // Ad presets
        $presets = [
            ['name' => 'Green Rounded', 'button_text' => 'Download Now', 'button_color' => '#01BF63', 'button_text_color' => '#ffffff', 'button_size' => 'medium', 'button_style' => 'rounded'],
            ['name' => 'Blue Pill', 'button_text' => 'Get It Free', 'button_color' => '#3b82f6', 'button_text_color' => '#ffffff', 'button_size' => 'large', 'button_style' => 'pill'],
            ['name' => 'Dark Square', 'button_text' => 'Install Now', 'button_color' => '#111827', 'button_text_color' => '#ffffff', 'button_size' => 'medium', 'button_style' => 'square'],
            ['name' => 'Orange Rounded', 'button_text' => 'Free Download', 'button_color' => '#f59e0b', 'button_text_color' => '#ffffff', 'button_size' => 'medium', 'button_style' => 'rounded'],
        ];
        foreach ($presets as $p) {
            AdPreset::firstOrCreate(['name' => $p['name']], $p);
        }

        // Country rates
        $rates = [
            ['US', 'United States', 0.000800],
            ['GB', 'United Kingdom', 0.000700],
            ['CA', 'Canada', 0.000650],
            ['AU', 'Australia', 0.000600],
            ['DE', 'Germany', 0.000550],
            ['FR', 'France', 0.000500],
            ['NL', 'Netherlands', 0.000480],
            ['SE', 'Sweden', 0.000460],
            ['NO', 'Norway', 0.000460],
            ['CH', 'Switzerland', 0.000480],
            ['JP', 'Japan', 0.000400],
            ['KR', 'South Korea', 0.000380],
            ['SG', 'Singapore', 0.000420],
            ['IN', 'India', 0.000080],
            ['BR', 'Brazil', 0.000100],
            ['PK', 'Pakistan', 0.000060],
            ['BD', 'Bangladesh', 0.000050],
            ['NG', 'Nigeria', 0.000060],
            ['XX', 'Unknown', 0.000020],
        ];
        foreach ($rates as [$code, $name, $rate]) {
            CountryRate::firstOrCreate(['country_code' => $code], ['country_name' => $name, 'rate_per_click' => $rate, 'is_active' => true]);
        }

        $this->command->info('✓ Admin: admin@installsbank.com / admin123456');
        $this->command->info('✓ Demo Publisher: publisher@demo.com / demo123456');
    }
}
