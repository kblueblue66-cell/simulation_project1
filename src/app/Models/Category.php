<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function items() { return $this->belongsToMany(Item::class); }
    /**
     * 更新用タイムスタンプのカラム名を仕様に合わせてカスタマイズ
     */
    const UPDATED_AT = 'update_at';
}
