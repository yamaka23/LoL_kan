<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatRune extends Model
{
    use HasFactory;

    // $incrementing と $keyType の記述は不要になります

    protected $fillable = [
        'api_id',
        'name',
        'icon_path',
        'tier',
        'slot_index',
    ];
}
