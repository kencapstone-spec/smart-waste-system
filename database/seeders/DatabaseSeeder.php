<?php

namespace Database\Seeders;

use App\Models\Street;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. ZONES (6 zones)
        // ============================================================
        $zones = collect([
            'Zone 1 - Poblacion',
            'Zone 2 - Riverside',
            'Zone 3 - Hillside',
            'Zone 4 - Market Area',
            'Zone 5 - Coastal',
            'Zone 6 - Uptown',
        ])->map(fn ($name) => Zone::create(['name' => $name]));

        // ============================================================
        // 2. STREETS (3 per zone = 18 streets)
        // ============================================================
        $streetNames = [
            ['Rizal St.', 'Mabini St.', 'Bonifacio St.'],
            ['Riverside Dr.', 'Calamansi Rd.', 'Sampaguita Lane'],
            ['Hilltop Rd.', 'Bayanihan St.', 'Mahogany Lane'],
            ['Mercado St.', 'Tiangge Rd.', 'Palengke Lane'],
            ['Seaside Blvd.', 'Coral St.', 'Dagat Rd.'],
            ['Upper Main Rd.', 'Sunrise Ave.', 'Panorama St.'],
        ];

        $streets = collect();
        foreach ($zones as $i => $zone) {
            foreach ($streetNames[$i] as $streetName) {
                $streets->push(Street::create([
                    'zone_id' => $zone->id,
                    'name' => $streetName,
                ]));
            }
        }

        // ============================================================
        // 3. USERS
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

        // -- Barangay Officials (3) --
        $official1 = User::factory()->official()->create([
            'name' => 'Kap. Roberto Santos',
            'phone' => '09333333333',
        ]);
        User::factory()->official()->create([
            'name' => 'Kag. Maria Garcia',
            'phone' => '09444444444',
        ]);

        // -- Personnel / Collectors (6) --
        $personnelData = [
            ['name' => 'Juan dela Cruz',   'phone' => '09555555555'],
        ];
        foreach ($personnelData as $p) {
            User::factory()->personnel()->create($p);
        }

        // -- Active Residents (25) spread across all 18 streets --
        $residentNames = [
            'Ana Villanueva', 'Liza Tan', 'Mario Aquino', 'Rosa Fernandez', 'Elena Pascual',
            'Ben Torres', 'Grace Lim', 'Ramon Castillo', 'Sofia Navarro', 'Carlo Ramos',
            'Mila Soriano', 'Jose Aguilar', 'Teresa Cruz', 'Noel Dizon', 'Lydia Manalo',
            'Christian Santos', 'Angelica Flores', 'Gabriel Cruz', 'Patricia Reyes', 'Jon Villanueva',
            'Maricel Soriano', 'Ricardo Diaz', 'Rowena Mercado', 'Fernando Poe', 'Imelda Marcos',
        ];

        foreach ($residentNames as $i => $name) {
            $street = $streets[$i % $streets->count()];
            User::factory()->resident()->create([
                'name' => $name,
                'phone' => '0944'.str_pad($i + 1, 7, '0', STR_PAD_LEFT),
                'street_id' => $street->id,
                'address' => 'Purok '.rand(1, 8).', '.$street->name,
                'approved_at' => now()->subDays(rand(5, 30)),
                'approved_by' => $official1->id,
            ]);
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
                ['Resident (active)',    '25', '09440000001 – 09440000025'],
                ['', '', ''],
                ['Zones',                '6',  'Structural Data'],
                ['Streets',              '18', 'Structural Data'],
            ]
        );
    }
}
