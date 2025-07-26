<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\ItemMaterial;
use App\Models\ItemRelation;

class Item extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
        'image',
        'description',
        'plaintext',
        'tags',
        'stats',
        'gold',
        'in_store',
        'purchasable',
        'language',
        'depth',
        'required_champion',
        'is_summoners_rift_available',
        'version',
        'colloq',
    ];

    protected $casts = [
        'tags' => 'array',
        'stats' => 'array',
        'gold' => 'array',
    ];

    /**
     * 投稿との多対多リレーション
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_item')->withPivot('order');
    }

    /**
     * この完成アイテムに使われている素材（ItemMaterial）との多対多リレーション
     */
    public function materials()
    {
        return $this->belongsToMany(ItemMaterial::class, 'item_relations', 'item_id', 'material_item_id')
                    ->withPivot('count');
    }

    /**
     * item_relations テーブルのリレーション
     */
    public function itemRelations()
    {
        return $this->hasMany(ItemRelation::class, 'item_id');
    }

    /**
     * 一般アイテムフィルター用クエリスコープ
     */
    public function scopeGeneral($query)
    {
        return $query->whereNull('required_champion')
                     ->where('is_summoners_rift_available', true)
                     ->where('in_store', true)
                     ->where('purchasable', true)
                     ->whereNotNull('gold->total')
                     ->where(function ($q) {
                         $q->whereJsonDoesntContain('tags', 'Consumable')
                           ->whereJsonDoesntContain('tags', 'Trinket')
                           ->whereJsonDoesntContain('tags', 'Jungle')
                           ->whereJsonDoesntContain('tags', 'GoldPer')
                           ->whereJsonDoesntContain('tags', 'Lane');
                     });
    }
}
