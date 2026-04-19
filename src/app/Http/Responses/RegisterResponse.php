<?php
namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // FN006: 初回会員登録直後はプロフィール設定画面(PG10)へ遷移
        return redirect('/mypage/profile');
    }
}
