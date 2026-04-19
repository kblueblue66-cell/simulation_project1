<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Session;

class AddressController extends Controller
{
    /**
     * 送付先住所変更画面（PG07）の表示
     */
    public function edit($item_id)
    {
        $user = Auth::user();

        // 1. セッションに変更途中の住所があるか確認
        // 2. なければプロフィールの登録住所を取得 (FN021)
        $address = Session::get('shipping_address_' . $item_id) ?? ($user->profile ??(object)[
            'post_code' => '',
            'address' => '',
            'building' => ''
        ]);

        if (is_array($address)) {
            $address = (object) $address;
        }

        return view('address', [
            'item_id' => $item_id,
            'address' => $address
        ]);
    }

    /**
     * 住所情報の更新処理（セッション保存）
     * FN024: 登録した住所を購入画面に反映させる
     */
    public function update(AddressRequest $request, $item_id)
    {
        // バリデーション済みの情報をセッションに一時保存
        Session::put('shipping_address_' . $item_id, $request->only([
            'post_code', 'address', 'building'
        ]));

        // 商品購入画面（PG06）へ戻る
        return redirect()->route('purchase.create', ['item_id' => $item_id]);
    }
}
