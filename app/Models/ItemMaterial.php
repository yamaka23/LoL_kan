<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\ItemRelation;

class ItemMaterial extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
        'image_url',
        'stats',
        'gold',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    /**
     * この素材が使われているアイテムとの多対多リレーション
     */
    public function usedInItems()
    {
        return $this->belongsToMany(Item::class, 'item_relations', 'material_item_id', 'item_id')
                    ->withPivot('count');
    }

    /**
     * 中間テーブル（item_relations）へのリレーション（必要があれば）
     */
    public function itemRelations()
    {
        return $this->hasMany(ItemRelation::class, 'material_item_id');
    }
}
