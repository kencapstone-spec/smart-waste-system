<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. ZONES (13 zones)
        // ============================================================
        $zoneNames = [
            'Centro',
            'Nabunturan',
            'Cadanoy',
            'YMCA',
            'Mahayahay',
            'Cahayag',
            'Acasia',
            'JBC',
            'Cogao',
            'Samco',
            'Toog',
            'Sambag',
            'Gutahit',
        ];

        $zones = collect($zoneNames)->map(fn ($name) => Zone::create(['name' => $name]));

        // ============================================================
        // 2. USERS
        // ============================================================

        // -- Super Admins (2) --
        User::factory()->superAdmin()->create([
            'name' => 'Kenneth Admin',
            'phone' => '09111111111',
        ]);
        User::factory()->superAdmin()->create([
            'name' => 'Kean Admin',
            'phone' => '09222222222',
        ]);

        // -- Barangay Officials (2) --
        $official1 = User::factory()->official()->create([
            'name' => 'Kap. Roberto Santos',
            'phone' => '09333333333',
        ]);
        User::factory()->official()->create([
            'name' => 'Kag. Maria Garcia',
            'phone' => '09444444444',
        ]);

        // -- Personnel / Collectors (1) --
        $personnelData = [
            ['name' => 'Juan dela Cruz',   'phone' => '09555555555'],
        ];
        foreach ($personnelData as $p) {
            User::factory()->personnel()->create($p);
        }



        // ============================================================
        // SUMMARY
        // ============================================================
        $this->command->info('');
        $this->command->info('=== SEEDING COMPLETE ===');
        $this->command->info('');
        $this->command->table(
            ['Role / Entity', 'Count', 'Login Phone Range'],
            [
                ['Super Admin',          '2',  '09111111111, 09222222222'],
                ['Barangay Official',    '2',  '09333333333, 09444444444'],
                ['Personnel / Collector', '1',  '09555555555'],

                ['', '', ''],
                ['Zones',                '13',  'Structural Data'],
            ]
        );
    }
}
