<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainRune; // MainRuneモデルをuseする

class Rune extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false; // id は整数でなくAPIのID（intでも文字列でも手動管理）
    protected $keyType = 'int';   // 文字列なら 'string' に変えてもOK

    protected $fillable = [
        'id',
        'main_rune_id',
        'name',
        'icon',
        'longDesc', // 長い説明文
        'slot_index', // ルーンのスロットインデックス
    ];
    public function mainRune()
    {
        return $this->belongsTo(MainRune::class, 'main_rune_id', 'id');
    }
}
