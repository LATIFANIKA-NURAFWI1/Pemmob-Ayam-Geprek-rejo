<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ╔══════════════════════════════════════════════════════╗
 * ║           AKUN USER — GEPREK REJO SYSTEM            ║
 * ╠══════════════════════════════════════════════════════╣
 * ║  ROLE      │ EMAIL                   │ PASSWORD      ║
 * ╠════════════╪═════════════════════════╪═══════════════╣
 * ║  Owner     │ owner@geprekrejo.com    │ owner123      ║
 * ║  Kasir 1   │ kasir1@geprekrejo.com   │ kasir123      ║
 * ║  Kasir 2   │ kasir2@geprekrejo.com   │ kasir123      ║
 * ║  KDS Dapur │ dapur@geprekrejo.com    │ dapur123      ║
 * ╚══════════════════════════════════════════════════════╝
 *
 * CATATAN: Menggunakan DB::table() langsung agar tidak terjadi
 * double-hashing (model User punya cast 'password' => 'hashed',
 * jadi Hash::make() di sini sudah cukup tanpa cast lagi).
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = [
            [
                'name'              => 'Yono Mas (Owner)',
                'email'             => 'owner@geprekrejo.com',
                'role'              => 'owner',
                'password'          => Hash::make('owner123'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Kasir Outlet 1',
                'email'             => 'kasir1@geprekrejo.com',
                'role'              => 'kasir',
                'password'          => Hash::make('kasir123'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Kasir Outlet 2',
                'email'             => 'kasir2@geprekrejo.com',
                'role'              => 'kasir',
                'password'          => Hash::make('kasir123'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'name'              => 'Tim Dapur (KDS)',
                'email'             => 'dapur@geprekrejo.com',
                'role'              => 'kds',
                'password'          => Hash::make('dapur123'),
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        foreach ($users as $user) {
            // Gunakan DB::table() langsung agar password tidak di-hash dua kali
            // (model User memiliki cast 'password' => 'hashed' yang akan re-hash)
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['🔑 Owner / Admin', 'owner@geprekrejo.com', 'owner123'],
                ['🖥️  Kasir Outlet 1', 'kasir1@geprekrejo.com', 'kasir123'],
                ['🖥️  Kasir Outlet 2', 'kasir2@geprekrejo.com', 'kasir123'],
                ['🍳 KDS Dapur', 'dapur@geprekrejo.com', 'dapur123'],
            ]
        );
    }
}
