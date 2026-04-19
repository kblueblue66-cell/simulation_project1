<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'item_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function item() { return $this->belongsTo(Item::class); }
    /**
     * 更新用タイムスタンプのカラム名を仕様に合わせてカスタマイズ
     */
    const UPDATED_AT = 'update_at';
}
