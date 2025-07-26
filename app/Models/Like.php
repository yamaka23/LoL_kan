<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Post;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'count',
    ];

    /**
     * ユーザーとのリレーション（多対一）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 投稿とのリレーション（多対一）
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
