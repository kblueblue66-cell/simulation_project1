<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;


class LikeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function store($item_id)
    {
    // ログインユーザーのIDを取得
        $user_id = Auth::id();

        // すでに「いいね」しているか確認
        $like = Like::where('user_id', $user_id)
                    ->where('item_id', $item_id)
                    ->first();

        if ($like) {
            // すでに存在する場合は削除（いいね解除） [13: FN018-3]
            $like->delete();
        } else {
            // 存在しない場合は新規作成（いいね登録） [13: FN018-1]
            Like::create([
                'user_id' => $user_id,
                'item_id' => $item_id,
            ]);
        }

        // 処理後は元の画面（商品詳細画面 PG05）へリダイレクト
        return back();
    }
}
