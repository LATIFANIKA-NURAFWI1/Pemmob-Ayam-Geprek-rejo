<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║              AKUN MEMBER — LOGIN VIA NO. HP                 ║
 * ╠══════════════════════════════════════════════════════════════╣
 * ║  Nama           │ No. HP        │ PIN    │ Poin   │ Tier    ║
 * ╠═════════════════╪═══════════════╪════════╪════════╪═════════╣
 * ║  Budi Santoso   │ 08123456789   │ 1234   │ 500    │ silver  ║
 * ║  Siti Rahayu    │ 08234567890   │ 1234   │ 2100   │ gold    ║
 * ║  Ahmad Fauzi    │ 08345678901   │ 1234   │ 50     │ bronze  ║
 * ║  Demo Member    │ 08111111111   │ 0000   │ 1000   │ silver  ║
 * ╚══════════════════════════════════════════════════════════════╝
 */
class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name'         => 'Budi Santoso',
                'phone'        => '08123456789',
                'pin'          => Hash::make('1234'),
                'points'       => 500,
                'total_orders' => 25,
                'total_spent'  => 625000,
                'tier'         => 'silver',
                'is_active'    => true,
            ],
            [
                'name'         => 'Siti Rahayu',
                'phone'        => '08234567890',
                'pin'          => Hash::make('1234'),
                'points'       => 2100,
                'total_orders' => 87,
                'total_spent'  => 2175000,
                'tier'         => 'gold',
                'is_active'    => true,
            ],
            [
                'name'         => 'Ahmad Fauzi',
                'phone'        => '08345678901',
                'pin'          => Hash::make('1234'),
                'points'       => 50,
                'total_orders' => 3,
                'total_spent'  => 75000,
                'tier'         => 'bronze',
                'is_active'    => true,
            ],
            [
                'name'         => 'Demo Member',
                'phone'        => '08111111111',
                'pin'          => Hash::make('0000'),
                'points'       => 1000,
                'total_orders' => 10,
                'total_spent'  => 350000,
                'tier'         => 'silver',
                'is_active'    => true,
            ],
        ];

        foreach ($members as $member) {
            Member::updateOrCreate(
                ['phone' => $member['phone']],
                $member
            );
        }

        $this->command->table(
            ['Nama', 'No. HP (Login)', 'PIN', 'Poin', 'Tier'],
            [
                ['Budi Santoso', '08123456789', '1234', '500', '🥈 silver'],
                ['Siti Rahayu', '08234567890', '1234', '2100', '🥇 gold'],
                ['Ahmad Fauzi', '08345678901', '1234', '50', '🥉 bronze'],
                ['Demo Member', '08111111111', '0000', '1000', '🥈 silver'],
            ]
        );
    }
}
