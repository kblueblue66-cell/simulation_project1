<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            
            // テーブル仕様書 [25: No.6] に基づくダミーデータ
            'post_code' => '123-4567',
            'address' => '東京都渋谷区道玄坂2-10-12',
            'building' => 'コーポコーチテク',
            // 仕様書上はtimestamp型となっているが、決済日時やフラグとして扱う
            'payment_method' => now(),
        ];
    }
}
