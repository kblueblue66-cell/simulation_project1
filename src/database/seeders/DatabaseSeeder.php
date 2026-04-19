<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            // 1. 親となるデータを先に登録（外部キーの依存先）
            UserSeeder::class,      // usersテーブル
            ConditionSeeder::class, // conditionsテーブル [1, 2]
            CategorySeeder::class,  // categoriesテーブル [1, 2]

            // 2. 親データに依存するデータを登録
            ItemSeeder::class,      // itemsテーブル（user_id, condition_idが必要）[1]
            ProfileSeeder::class,
            // 3. その他、商品に紐づく中間テーブルや購入データなど
            // ProfileSeeder::class,
            // OrderSeeder::class,
        ]);
    }
}
