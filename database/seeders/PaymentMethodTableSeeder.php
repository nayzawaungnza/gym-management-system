<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class PaymentMethodTableSeeder extends Seeder
{
    protected $payment_methods = [
        [
            'display_name' => 'KBZ bank',
            'provider_name' => 'KBZ Direct Pay',
            'method_name' => 'PWA',
            'expire_minutes' => '30',
            'payment_logo' => 'assets/logo/KBZ.png',
        ],
        [
            'display_name' => 'KBZ Pay',
            'provider_name' => 'KBZ Pay',
            'method_name' => 'PWA',
            'expire_minutes' => '100',
            'payment_logo' => 'assets/logo/KBZPay.jpg',
        ],
        [
            'display_name' => 'Citizens Pay',
            'provider_name' => 'Citizens',
            'method_name' => 'PIN',
            'expire_minutes' => '10',
            'payment_logo' => 'assets/logo/citizens.png',
        ],
        [
            'display_name' => 'Wave Pay',
            'provider_name' => 'Wave Pay',
            'method_name' => 'PIN',
            'expire_minutes' => '100',
            'payment_logo' => 'assets/logo/wave.jpg',
        ],
        [
            'display_name' => 'AYA Pay',
            'provider_name' => 'AYA Pay',
            'method_name' => 'PIN',
            'expire_minutes' => '30',
            'payment_logo' => 'assets/logo/aya.png',
        ],
        [
            'display_name' => 'UAB Pay',
            'provider_name' => 'UAB Pay',
            'method_name' => 'PIN',
            'expire_minutes' => '3',
            'payment_logo' => 'assets/logo/uab.jpg',
        ],
        [
            'display_name' => 'Onepay',
            'provider_name' => 'Onepay',
            'method_name' => 'PIN',
            'expire_minutes' => '6',
            'payment_logo' => 'assets/logo/onepay.jpg',
        ],
        [
            'display_name' => 'CB Pay',
            'provider_name' => 'CB Pay',
            'method_name' => 'QR',
            'expire_minutes' => '3',
            'payment_logo' => 'assets/logo/cb.jpg',
        ],
        [
            'display_name' => 'MAB Bank',
            'provider_name' => 'MAB Bank',
            'method_name' => 'PIN',
            'expire_minutes' => '10',
            'payment_logo' => 'assets/logo/mab.png',
        ],
        [
            'display_name' => 'Cash On Delivery',
            'provider_name' => 'Cash',
            'method_name' => 'Cash',
            'expire_minutes' => '0',
            'payment_logo' => 'assets/logo/cod.png',
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('payment_methods')->truncate();
        foreach ($this->payment_methods as $payment_method) {
            PaymentMethod::create([
                'display_name' => $payment_method['display_name'],
                'provider_name' => $payment_method['provider_name'],
                'method_name' => $payment_method['method_name'],
                'expire_minutes' => $payment_method['expire_minutes'],
                'payment_logo' => $payment_method['payment_logo'],
                'is_active' => 1
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}