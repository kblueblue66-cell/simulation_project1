<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'post_code' => $this->faker->postcode, // 郵便番号 [25: No.2]
            'address' => $this->faker->address,   // 住所 [25: No.2]
            'building' => $this->faker->secondaryAddress, // 建物名 [25: No.2]
        ];
    }
}
