<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // リレーション先を自動生成
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            // バリデーションルール：入力必須、最大文字数255 [1]
            'content' => $this->faker->realText(100), 
        ];
    }
}
