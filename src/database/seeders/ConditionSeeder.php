<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //商品データ一覧 [2] および基本設計書 [4] に基づく商品の状態
        $conditions = [
            ['name' => '良好'],
            ['name' => '目立った傷や汚れなし'],
            ['name' => 'やや傷や汚れあり'],
            ['name' => '状態が悪い'],
        ];

        foreach ($conditions as $condition) {
            Condition::create($condition);
        }
    }
}

