<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\ItemMaterial;

class ItemRelation extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'item_id',
        'material_item_id',
        'count',
    ];

    /**
     * 完成品アイテム（Item）へのリレーション
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 素材アイテム（ItemMaterial）へのリレーション
     */
    public function material()
    {
        return $this->belongsTo(ItemMaterial::class, 'material_item_id');
    }
}
