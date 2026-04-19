<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;


class ItemController extends Controller
{
    public function index(Request $request){
         // 1. 検索キーワードの取得 (FN016)
        $keyword = $request->input('keyword');

        $query = Item::query();

        if (Auth::check()) {
    // ログインしている時だけ、自分以外のユーザーの商品に絞り込む
        $query->where('user_id', '!=', Auth::id());
        }

        // 3. 部分一致検索の実装 (FN016-2)
        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        // 4. タブによる切り替え (FN015)
        $tab = $request->query('tab');
        if ($tab === 'mylist') {
            // 未認証の場合は何も表示しない (FN015-4)
            if (!Auth::check()) {
                $items = collect();
            } else {
                // いいねした商品のみを取得 (FN015-1)
                $items = Auth::user()->likedItems()
                    ->with('order')
                    ->whereIn('items.id',$query->pluck('id'))
                    ->get();
            }
        } else {
            $items = $query->with('order')->get();
        }

        return view('index',compact('items','keyword'));
    }
    public function __construct(){
        // index（一覧）と show（詳細）はログインなしでもアクセス可能にする [18: FN017-3]
        $this->middleware('auth')->except(['index', 'show']);
    }
    public function show($item_id){
    // 指定されたIDの商品を取得し、関連する情報をロードする
    $item = Item::with(['condition', 'categories', 'comments.user.profile'])->withCount(['likes','comments'])->findOrFail($item_id);

    // 商品詳細画面（show.blade.php）を表示
    return view('show', compact('item'));
    }
    public function store(ExhibitionRequest $request)
    {
        $user = auth()->user();

    // 画像をstorageディレクトリに保存 (FN029)
        $imagePath = $request->file('image')->store('items','public');

        // 商品情報の登録 (FN028)
        $item = Item::create([
            'user_id' => $user->id,
            'condition_id' => $request->condition_id,
            'name' => $request->name,
            'brand' => $request->brand,
            'price' => $request->price,
            'description' => $request->description,
            'image_url' => $imagePath,
        ]);

        // カテゴリーの紐付け（複数選択対応） (FN028-1-2-2)
        $item->categories()->sync($request->category_ids);

        return redirect('/');
    }
    public function create()
    {
        // 1. 全てのカテゴリーを取得 [17: FN028-2]
        // ビューでチェックボックスとして表示するために必要
        $categories = Category::all();

        // 2. 全ての商品状態を取得 [17: FN028-3]
        // ビューでプルダウンメニューとして表示するために必要
        $conditions = Condition::all();

        // 3. 商品出品ビュー（sell.blade.php）にデータを渡して表示
        return view('sell', compact('categories', 'conditions'));
    }
    public function storeComment(CommentRequest $request, $item_id)
    {
        // 1. バリデーションの実行
        // CommentRequest を引数に指定することで、自動的に「必須・255文字以内」の検証が行われます [2]

        // 2. コメントデータの登録 [25: No.8]
        Comment::create([
            'user_id' => Auth::id(),      // ログインユーザーのID [18: FN020-1]
            'item_id' => $item_id,         // URLパラメータから取得した商品ID
            'content' => $request->content, // フォームの入力内容
        ]);

        // 3. 商品詳細画面（PG05）へリダイレクトして戻る [2, 3]
        return redirect()->route('item.show', ['item_id' => $item_id])->with('message', 'コメントを投稿しました');
    }
}
