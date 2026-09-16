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

        $zones = collect($zoneNames)->map(fn ($name) => Zone::firstOrCreate(['name' => $name]));

        // ============================================================
        // 2. USERS
        // ============================================================

        // -- Super Admins (2) --
        User::firstOrCreate(
            ['phone' => '09111111111'],
            ['name' => 'Kenneth Admin', 'role' => 'super_admin', 'status' => 'active', 'address' => 'Purok Centro']
        );
        User::firstOrCreate(
            ['phone' => '09222222222'],
            ['name' => 'Kean Admin', 'role' => 'super_admin', 'status' => 'active', 'address' => 'Purok Centro']
        );

        // -- Barangay Officials (2) --
        User::firstOrCreate(
            ['phone' => '09333333333'],
            ['name' => 'Kap. Roberto Santos', 'role' => 'barangay_official', 'status' => 'active', 'address' => 'Barangay Hall']
        );
        User::firstOrCreate(
            ['phone' => '09444444444'],
            ['name' => 'Kag. Maria Garcia', 'role' => 'barangay_official', 'status' => 'active', 'address' => 'Barangay Hall']
        );

        // -- Personnel / Collectors (1) --
        User::firstOrCreate(
            ['phone' => '09555555555'],
            ['name' => 'Juan dela Cruz', 'role' => 'personnel', 'status' => 'active', 'address' => 'Barangay San Isidro']
        );

        // -- Residents (3) --
        $cahayagZone = $zones->where('name', 'Cahayag')->first();
        $zoneId = $cahayagZone ? $cahayagZone->id : ($zones->first()->id ?? null);

        User::firstOrCreate(
            ['phone' => '09666666666'],
            ['name' => 'Kenneth', 'role' => 'resident', 'status' => 'active', 'address' => 'Purok Cahayag', 'zone_id' => $zoneId]
        );
        User::firstOrCreate(
            ['phone' => '09777777777'],
            ['name' => 'Kean', 'role' => 'resident', 'status' => 'active', 'address' => 'Purok Cahayag', 'zone_id' => $zoneId]
        );
        User::firstOrCreate(
            ['phone' => '09888888888'],
            ['name' => 'Lester', 'role' => 'resident', 'status' => 'active', 'address' => 'Purok Cahayag', 'zone_id' => $zoneId]
        );

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
                ['Residents',            '3',  '09666666666, 09777777777, 09888888888'],

                ['', '', ''],
                ['Zones',                '13',  'Structural Data'],
            ]
        );
    }
}
