<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunePath extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'integer';
    protected $fillable = ['id', 'key', 'name', 'icon_path'];

    public function runes()
    {
        return $this->hasMany(Rune::class);
    }
}
