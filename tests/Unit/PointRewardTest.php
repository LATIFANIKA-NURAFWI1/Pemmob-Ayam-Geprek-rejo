<?php

use App\Models\Member;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Voucher;
use App\Services\PointService;
use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('points formula is 1 point per 1000 rupiah', function () {
    $pointService = new PointService();
    
    expect($pointService->calculateEarnedPoints(10000))->toBe(10);
    expect($pointService->calculateEarnedPoints(15400))->toBe(15);
    expect($pointService->calculateEarnedPoints(900))->toBe(0);
});

test('auto redeem reward at 150 points', function () {
    $pointService = new PointService();
    
    $member = Member::create([
        'name' => 'John Doe',
        'phone' => '08123456780',
        'pin' => '123456',
        'points' => 320,
    ]);

    $lastCode = $pointService->checkAndAutoRedeemReward($member);
    
    $member->refresh();
    
    // 320 points should convert twice (150 * 2 = 300) leaving 20 points
    expect($member->points)->toBe(20);
    expect($lastCode)->not->toBeNull();
    expect(str_starts_with($lastCode, 'FREE-GEPREK-'))->toBeTrue();

    // Verify 2 vouchers were created
    $vouchers = Voucher::where('code', 'like', 'FREE-GEPREK-%')->get();
    expect($vouchers)->toHaveCount(2);

    $vouchers->each(function ($voucher) use ($member) {
        expect($voucher->discount_type)->toBe('fixed');
        expect($voucher->max_uses)->toBe(1);
        expect($voucher->member_id)->toBe($member->id);
    });

    // Verify session has the codes
    $sessionCodes = session('reward_vouchers_redeemed');
    expect($sessionCodes)->toHaveCount(2);
});

test('free geprek voucher discounts the exact price of geprek item in cart', function () {
    $voucherService = new VoucherService();
    
    // Create categories & menu items
    $category = Category::create(['name' => 'Food', 'slug' => 'food']);
    $geprekOriginal = MenuItem::create([
        'name' => 'Paket Nasi Ayam Geprek',
        'price' => 15000,
        'category_id' => $category->id,
        'is_available' => true,
    ]);
    $drink = MenuItem::create([
        'name' => 'Es Teh Manis',
        'price' => 5000,
        'category_id' => $category->id,
        'is_available' => true,
    ]);

    // Create a free geprek voucher
    $code = 'FREE-GEPREK-TEST1';
    $voucher = Voucher::create([
        'code' => $code,
        'name' => 'Gratis Geprek',
        'discount_type' => 'fixed',
        'discount_value' => 15000,
        'max_uses' => 1,
        'is_active' => true,
    ]);

    // Cart without geprek item should fail validation
    $cartWithoutGeprek = [
        $drink->id => [
            'id' => $drink->id,
            'name' => $drink->name,
            'price' => $drink->price,
            'quantity' => 1,
        ]
    ];

    expect(fn () => $voucherService->validateAndCalculate($code, 5000, null, $cartWithoutGeprek))
        ->toThrow(\InvalidArgumentException::class, 'Voucher ini hanya dapat digunakan jika terdapat menu Paket Nasi Ayam Geprek di dalam keranjang.');

    // Cart with geprek item should succeed and discount the exact geprek price
    $cartWithGeprek = [
        $geprekOriginal->id => [
            'id' => $geprekOriginal->id,
            'name' => $geprekOriginal->name,
            'price' => $geprekOriginal->price,
            'quantity' => 1,
        ],
        $drink->id => [
            'id' => $drink->id,
            'name' => $drink->name,
            'price' => $drink->price,
            'quantity' => 1,
        ]
    ];

    $result = $voucherService->validateAndCalculate($code, 20000, null, $cartWithGeprek);
    expect($result['discount'])->toBe(15000.00);
});
