<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'image_url', 'post_code', 'address', 'building'
    ];

    public function user() { return $this->belongsTo(User::class); }

    /**
     * 更新用タイムスタンプのカラム名を仕様に合わせてカスタマイズ
     */
    const UPDATED_AT = 'update_at';
}
