<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rune extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'integer';
    protected $fillable = [
        'id',
        'name',
        'rune_path_id',
        'tier',
        'slot_index',
        'icon_path',
        'longDesc',
    ];

    public function runePath()
    {
        return $this->belongsTo(RunePath::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_rune');
    }
}
