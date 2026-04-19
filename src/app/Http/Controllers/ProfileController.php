<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;


class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page','sell');

        if($page === 'buy'){
            $items = Item::whereHas('order',function($query) use($user){
                $query->where('user_id',$user->id);
            })->get();
        }else{
            $items = Item::where('user_id',$user->id)->get();
        }
        return view('mypage',compact('user','items','page'));
    }
    public function edit(){
        $user = Auth::user();
        return view('profile',compact('user'));
    }
    public function update(ProfileRequest $request){
        $user = Auth::user();

        // 1. ユーザー名の更新 (usersテーブル) [1]
        $user->update([
            'name' => $request->name,
        ]);

        // 2. プロフィール画像の保存処理 (FN027) [2]
        $profileData = [
            'post_code' => $request->post_code,
            'address' => $request->address,
            'building' => $request->building,
        ];

        if ($request->hasFile('image')) {
            // 画像を storage/app/public/profiles に保存し、パスを取得
            $path = $request->file('image')->store('profiles', 'public');
            $profileData['image_url'] = $path;
        }

        // 3. プロフィール情報の保存 (profilesテーブル) [1]
        // すでにプロフィールがある場合は更新、ない場合は新規作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('profile.edit')->with('message', 'プロフィールを更新しました');
    }
}
