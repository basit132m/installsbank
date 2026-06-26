<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LanderSetting;

class LanderController extends Controller
{
    private static array $schemes = [
        'dark-red'     => ['bg_from'=>'#0f0a0a','bg_to'=>'#1a0505','card'=>'rgba(20,10,10,0.82)','accent'=>'#dc2626','accent2'=>'#991b1b','glow'=>'rgba(220,38,38,0.25)','text_accent'=>'#fca5a5'],
        'dark-blue'    => ['bg_from'=>'#080d1a','bg_to'=>'#030a1a','card'=>'rgba(8,13,30,0.85)','accent'=>'#2563eb','accent2'=>'#1d4ed8','glow'=>'rgba(37,99,235,0.25)','text_accent'=>'#93c5fd'],
        'dark-green'   => ['bg_from'=>'#030f0a','bg_to'=>'#010f07','card'=>'rgba(3,15,10,0.85)','accent'=>'#059669','accent2'=>'#047857','glow'=>'rgba(5,150,105,0.25)','text_accent'=>'#6ee7b7'],
        'dark-purple'  => ['bg_from'=>'#0a060f','bg_to'=>'#06030f','card'=>'rgba(10,6,15,0.85)','accent'=>'#7c3aed','accent2'=>'#6d28d9','glow'=>'rgba(124,58,237,0.25)','text_accent'=>'#c4b5fd'],
        'neon-cyan'    => ['bg_from'=>'#030a10','bg_to'=>'#010610','card'=>'rgba(3,10,16,0.85)','accent'=>'#06b6d4','accent2'=>'#0891b2','glow'=>'rgba(6,182,212,0.25)','text_accent'=>'#a5f3fc'],
        'amber-gold'   => ['bg_from'=>'#100900','bg_to'=>'#0f0600','card'=>'rgba(16,9,0,0.85)','accent'=>'#d97706','accent2'=>'#b45309','glow'=>'rgba(217,119,6,0.25)','text_accent'=>'#fcd34d'],
        'rose-pink'    => ['bg_from'=>'#100009','bg_to'=>'#0f0008','card'=>'rgba(16,0,9,0.85)','accent'=>'#ec4899','accent2'=>'#db2777','glow'=>'rgba(236,72,153,0.25)','text_accent'=>'#f9a8d4'],
        'orange-ember' => ['bg_from'=>'#100600','bg_to'=>'#0f0400','card'=>'rgba(16,6,0,0.85)','accent'=>'#f97316','accent2'=>'#ea580c','glow'=>'rgba(249,115,22,0.25)','text_accent'=>'#fdba74'],
        'teal'         => ['bg_from'=>'#010f0e','bg_to'=>'#000f0d','card'=>'rgba(1,15,14,0.85)','accent'=>'#14b8a6','accent2'=>'#0d9488','glow'=>'rgba(20,184,166,0.25)','text_accent'=>'#5eead4'],
        'indigo'       => ['bg_from'=>'#06060f','bg_to'=>'#04040f','card'=>'rgba(6,6,15,0.85)','accent'=>'#6366f1','accent2'=>'#4f46e5','glow'=>'rgba(99,102,241,0.25)','text_accent'=>'#a5b4fc'],
        'lime-green'   => ['bg_from'=>'#060f00','bg_to'=>'#040f00','card'=>'rgba(6,15,0,0.85)','accent'=>'#84cc16','accent2'=>'#65a30d','glow'=>'rgba(132,204,22,0.25)','text_accent'=>'#bef264'],
        'silver'       => ['bg_from'=>'#08090f','bg_to'=>'#06070f','card'=>'rgba(8,9,15,0.85)','accent'=>'#94a3b8','accent2'=>'#64748b','glow'=>'rgba(148,163,184,0.25)','text_accent'=>'#e2e8f0'],
    ];

    public function show()
    {
        $settings = LanderSetting::current();

        // Increment real visit counter atomically (no race condition)
        LanderSetting::where('id', 1)->increment('visit_count');
        $settings->visit_count += 1;

        $scheme       = self::$schemes[$settings->color_scheme] ?? self::$schemes['dark-red'];
        $displayCount = $settings->download_count + $settings->visit_count;

        return view('public.lander', compact('settings', 'scheme', 'displayCount'));
    }
}
