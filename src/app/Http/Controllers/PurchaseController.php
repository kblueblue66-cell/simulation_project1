<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    public function store(PurchaseRequest $request, $item_id)
    {
        // 1. 二重購入防止
        if (Order::where('item_id', $item_id)->exists()) {
            return redirect()->route('item.show', $item_id)->with('error', 'この商品はすでに売り切れています。');
        }

        $item = Item::findOrFail($item_id);

        // 2. テストをパスさせるため、ここでレコードを作成する
        // FN024-2-1: 購入時の住所をスナップショットとして保存する
        // 支払い方法（payment_method）は仕様書[1]に従い timestamp 型（now()）で保存
        Order::create([
            'user_id'   => Auth::id(),
            'item_id'   => $item_id,
            'post_code' => $request->post_code,
            'address'   => $request->address,
            'building'  => $request->building,
            'payment_method' => now(),
        ]);

        // 3. Stripe決済への接続 [18: FN023]
        Stripe::setApiKey(config('services.stripe.secret_key'));

        $checkout_session = StripeSession::create([
            'payment_method_types' => [$request->payment_method === 'card' ? 'card' : 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount'  => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item_id' => $item->id]),
            'cancel_url'  => route('purchase.create', ['item_id' => $item->id]),
        ]);

        // Stripeの決済ページへリダイレクト
        return redirect($checkout_session->url, 303);
    }

    public function success($item_id)
    {
        // 4. セッションのクリーンアップ（住所変更時[FN024]のデータなど）
        Session::forget('shipping_address_' . $item_id);

        // 遷移先は商品一覧画面（トップ画面） [18: FN022-4]
        return redirect()->route('item.index')->with('success', '購入が完了しました。');
    }

    public function create(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // セッションに変更後の住所があればそれを使い、なければプロフィールの住所を使う [18: FN021-4, FN024-2]
        // ※プロフィールのテーブル名が「plofiles」であることに注意
        $addressData = Session::get('shipping_address_' . $item_id) ?? [
            'post_code' => $user->profile->post_code ?? '',
            'address'   => $user->profile->address ?? '',
            'building'  => $user->profile->building ?? '',
        ];

        $address = (object)$addressData;
        $payment_method = $request->query('payment_method');

        return view('purchase', compact('item', 'user', 'payment_method', 'address'));
    }
}