<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class Champion extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'image',
        'version',
        'language',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // App\Models\Champion.php

    public function getRouteKeyName()
    {
        return 'id';  // id が文字列の場合でもこれでOK
    }

}
