<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code'             => 'GEPREK10',
                'name'             => 'Diskon 10% untuk Semua Menu',
                'description'      => 'Voucher diskon 10% berlaku untuk semua menu, minimum belanja Rp 20.000',
                'discount_type'    => 'percentage',
                'discount_value'   => 10,
                'minimum_order'    => 20000,
                'maximum_discount' => 15000,
                'max_uses'         => 100,
                'is_active'        => true,
                'member_only'      => false,
                'starts_at'        => now(),
                'expires_at'       => now()->addMonths(3),
            ],
            [
                'code'             => 'MEMBER5K',
                'name'             => 'Diskon Rp 5.000 Khusus Member',
                'description'      => 'Diskon flat Rp 5.000 untuk member terdaftar, minimum belanja Rp 30.000',
                'discount_type'    => 'fixed',
                'discount_value'   => 5000,
                'minimum_order'    => 30000,
                'maximum_discount' => null,
                'max_uses'         => 200,
                'is_active'        => true,
                'member_only'      => true,
                'starts_at'        => now(),
                'expires_at'       => now()->addMonths(6),
            ],
            [
                'code'             => 'OPENING25',
                'name'             => 'Promo Grand Opening 25%',
                'description'      => 'Diskon 25% maksimal Rp 25.000, berlaku terbatas 50 penggunaan',
                'discount_type'    => 'percentage',
                'discount_value'   => 25,
                'minimum_order'    => 15000,
                'maximum_discount' => 25000,
                'max_uses'         => 50,
                'is_active'        => true,
                'member_only'      => false,
                'starts_at'        => now(),
                'expires_at'       => now()->addWeeks(2),
            ],
            [
                'code'             => 'GRATIS',
                'name'             => 'Test Voucher Unlimited',
                'description'      => 'Voucher testing tanpa batas waktu dan penggunaan (untuk development)',
                'discount_type'    => 'fixed',
                'discount_value'   => 10000,
                'minimum_order'    => 0,
                'maximum_discount' => null,
                'max_uses'         => 0, // unlimited
                'is_active'        => true,
                'member_only'      => false,
                'starts_at'        => null,
                'expires_at'       => null,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::updateOrCreate(
                ['code' => $voucher['code']],
                $voucher
            );
        }

        $this->command->table(
            ['Kode', 'Nama', 'Tipe', 'Nilai', 'Min. Order', 'Member Only'],
            [
                ['GEPREK10',  'Diskon 10%',         '%',       '10%',        'Rp 20.000', 'Tidak'],
                ['MEMBER5K',  'Diskon 5rb Member',  'Nominal', 'Rp 5.000',   'Rp 30.000', 'YA'],
                ['OPENING25', 'Grand Opening 25%',  '%',       '25% maks 25rb','Rp 15.000', 'Tidak'],
                ['GRATIS',    'Test Unlimited',     'Nominal', 'Rp 10.000',  'Rp 0',      'Tidak'],
            ]
        );
    }
}
