<?php
namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        // ログアウト後は商品一覧画面（PG01）へ遷移させる [1]
        return redirect('/');
    }
}