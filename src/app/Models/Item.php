<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'condition_id', 'name', 'brand', 'price', 'description', 'image_url'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function condition() { return $this->belongsTo(Condition::class); }
    // 多対多: カテゴリー
    public function categories() { return $this->belongsToMany(Category::class,'category_item'); }
    public function likes() { return $this->hasMany(Like::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function order() { return $this->hasOne(Order::class); }
    public function isSold():bool{
        return $this->order()->exists();
    }
    /**
     * 指定したユーザーがこの商品にいいねしているか判定する [17: FN018]
     *
     * @param \App\Models\User|null $user
     * @return bool
     */
    public function isLikedBy($user): bool
    {
        if (!$user) {
            return false;
        }

        // すでにこのユーザーのIDがlikesテーブルに存在するかチェック
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}

