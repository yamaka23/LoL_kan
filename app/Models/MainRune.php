<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rune; // Runeモデルをuseする

class MainRune extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    
    protected $fillable = [
        'id',
        'name',
        'icon',
    ];
    public function runes()
    {
        return $this->hasMany(Rune::class);
    }
}
