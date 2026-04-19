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
        if(Order::where('item_id',$item_id)->exists()){
            return redirect()->route('item.show',$item_id)->with('error','この商品はすでに売り切れています。');
        }

        $item = Item::findOrFail($item_id);

        // 決済成功後にDB登録するため、一時的にリクエスト内容をセッションに保持 [18: FN024]
        Session::put('pending_purchase_' . $item_id, $request->only([
            'post_code', 'address', 'building', 'payment_method'
        ]));

        // 重要：StripeにAPIキーをセットする
        // configのパスが 'services.stripe.secret_key' の場合はそれに合わせてください
        Stripe::setApiKey(config('services.stripe.secret_key'));

        // StripeSession::create を使用（LaravelのSessionとの衝突回避）
        $checkout_session = StripeSession::create([
            'payment_method_types' => [$request->payment_method === 'card' ? 'card' : 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item_id' => $item->id]),
            'cancel_url' => route('purchase.create', ['item_id' => $item->id]),
        ]);

        return redirect($checkout_session->url,303);
    }
    /**
 * 購入完了後の処理
*/
    public function success($item_id)
    {
        $purchaseData = Session::get('pending_purchase_' . $item_id);

        if ($purchaseData) {
            // 決済成功を確認してからDBに保存 [25: No. 6]
            Order::create([
                'user_id'   => Auth::id(),
                'item_id'   => $item_id,
                'post_code' => $purchaseData['post_code'],
                'address'   => $purchaseData['address'],
                'building'  => $purchaseData['building'],
                'payment_method' => now(), // 仕様書の型がtimestampのため
            ]);
        }

        // 不要になったセッションのクリーンアップ
        Session::forget('pending_purchase_' . $item_id);
        Session::forget('shipping_address_' . $item_id);

        // 遷移先は商品一覧画面（トップ画面） [18: FN022-4]
        return redirect()->route('item.index')->with('success', '購入が完了しました。');
    }
    public function create(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

    // セッションに変更後の住所があればそれを使い、なければプロフィールの住所を使う [18: FN021-4, FN024-2]
        $addressData = Session::get('shipping_address_' . $item_id) ?? [
        'post_code' => $user->profile->post_code ?? '',
        'address'   => $user->profile->address ?? '',
        'building'  => $user->profile->building ?? '',
    ];

        $address = (object)$addressData;
    // クエリパラメータから支払い方法を取得し、ビューに渡す [22: ID 11]
        $payment_method = $request->query('payment_method');

        return view('purchase', compact('item', 'user', 'payment_method','address'));
    }
}
