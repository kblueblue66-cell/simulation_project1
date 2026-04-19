<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;


class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // ユーザーID: Userファクトリを呼び出して自動生成 [25: No.3]
            'user_id' => User::factory(),

            // 商品の状態ID: 1（良好など）をデフォルトに設定 [25: No.3]
            'condition_id' => \App\Models\Condition::factory(),

            // 商品名: 商品データ一覧にあるような名称を生成 [23, 25: No.3]
            'name' => $this->faker->unique()->word,

            // ブランド名: 任意項目 [23, 25: No.3]
            'brand' => 'テストブランド',

            // 販売価格: 0円以上の数値 [23, 24, 25: No.3]
            'price' => $this->faker->numberBetween(100, 50000),

            // 商品の説明: 最大255文字 [24, 25: No.3]
            'description' => 'これはテスト用の商品説明です。',

            // 商品画像URL: ソースにあるダミーURLなどを指定 [23, 25: No.3]
            'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
        ];
    }
}
