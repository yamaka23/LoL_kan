<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * マスアサインメントが可能な属性
     */
    protected $fillable = [
        'user_id',
        'champion_id',
        'vs_champion_id',
        'lane_id',
        'title',
        'content',
    ];

    /**
     * 投稿者（ユーザー）とのリレーション (多対1)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 使用チャンピオンとのリレーション (多対1)
     */
    public function champion()
    {
        return $this->belongsTo(Champion::class);
    }

    /**
     * 対戦相手のチャンピオンとのリレーション (多対1)
     */
    public function vsChampion()
    {
        return $this->belongsTo(Champion::class, 'vs_champion_id');
    }

    /**
     * レーンとのリレーション (多対1)
     */
    public function lane()
    {
        return $this->belongsTo(Lane::class);
    }

    /**
     * アイテムとのリレーション (多対多)
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'post_item')->withPivot('order');
    }

    /**
     * 選択されたルーンとのリレーション (多対多)
     * これが新しい、正しいリレーションの定義です。
     */
    public function runes()
    {
        // 中間テーブル 'post_rune' を経由して、たくさんの Rune を持つ
        return $this->belongsToMany(Rune::class, 'post_rune');
    }

    public function statRunes()
    {
        return $this->belongsToMany(StatRune::class, 'post_stat_rune');
    }
}
