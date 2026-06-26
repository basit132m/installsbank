<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LanderSetting;

class LanderController extends Controller
{
    private static array $schemes = [
        'dark-red'    => ['bg_from'=>'#0f0a0a','bg_to'=>'#1a0505','card'=>'rgba(20,10,10,0.82)','accent'=>'#dc2626','accent2'=>'#991b1b','glow'=>'rgba(220,38,38,0.25)','text_accent'=>'#fca5a5'],
        'dark-blue'   => ['bg_from'=>'#080d1a','bg_to'=>'#030a1a','card'=>'rgba(8,13,30,0.85)','accent'=>'#2563eb','accent2'=>'#1d4ed8','glow'=>'rgba(37,99,235,0.25)','text_accent'=>'#93c5fd'],
        'dark-green'  => ['bg_from'=>'#030f0a','bg_to'=>'#010f07','card'=>'rgba(3,15,10,0.85)','accent'=>'#059669','accent2'=>'#047857','glow'=>'rgba(5,150,105,0.25)','text_accent'=>'#6ee7b7'],
        'dark-purple' => ['bg_from'=>'#0a060f','bg_to'=>'#06030f','card'=>'rgba(10,6,15,0.85)','accent'=>'#7c3aed','accent2'=>'#6d28d9','glow'=>'rgba(124,58,237,0.25)','text_accent'=>'#c4b5fd'],
        'neon-cyan'   => ['bg_from'=>'#030a10','bg_to'=>'#010610','card'=>'rgba(3,10,16,0.85)','accent'=>'#06b6d4','accent2'=>'#0891b2','glow'=>'rgba(6,182,212,0.25)','text_accent'=>'#a5f3fc'],
        'amber-gold'  => ['bg_from'=>'#100900','bg_to'=>'#0f0600','card'=>'rgba(16,9,0,0.85)','accent'=>'#d97706','accent2'=>'#b45309','glow'=>'rgba(217,119,6,0.25)','text_accent'=>'#fcd34d'],
    ];

    public function show()
    {
        $settings = LanderSetting::current();
        $scheme   = self::$schemes[$settings->color_scheme] ?? self::$schemes['dark-red'];

        return view('public.lander', compact('settings', 'scheme'));
    }
}
