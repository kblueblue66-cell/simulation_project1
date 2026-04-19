<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //ユーザー情報のダミーデータ
        User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'), // 8文字以上のパスワード [3]
        ]);

        // 必要に応じて複数のテストユーザーを追加できます
        User::create([
            'name' => '出品者 太郎',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
