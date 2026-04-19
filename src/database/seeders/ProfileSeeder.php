<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\User;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //全てのユーザーを取得
        $users = User::all();

        foreach ($users as $user) {
            Profile::create([
                'user_id' => $user->id,
                // 画像URL（初期はnullまたはダミーパス）
                'image_url' => null,
                // 基本設計書 準拠：ハイフンありの8文字
                'post_code' => '123-4567',
                'address' => '東京都渋谷区道玄坂2-11-1',
                'building' => 'コーポコーチテック101',
            ]);
        }
    }
}
