<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rune;
use App\Models\Item;
use App\Models\Lane;
use App\Models\Champion;
use App\Models\User;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'champion_id',
        'vs_champion_id',
        'lane_id',
        'title',
        'content',
    ];

    // 投稿者（ユーザー）: 1対多（逆）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 使用チャンピオン
    public function champion()
    {
        return $this->belongsTo(Champion::class, 'champion_id', 'id');
    }

    // 対戦相手のチャンピオン
    public function vsChampion()
    {
        return $this->belongsTo(Champion::class, 'vs_champion_id', 'id');
    }

    // レーン
    public function lane()
    {
        return $this->belongsTo(Lane::class);
    }

    // ルーン（多対多）
    public function runes()
    {
        return $this->belongsToMany(Rune::class);
    }

    // アイテム（多対多、順序付き）
    public function items()
    {
        return $this->belongsToMany(Item::class, 'post_item')->withPivot('order');
    }
}
