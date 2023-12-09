<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ["id", "user_id", "title", "content", "image", "short_description"];

    // * relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select("id", "name", "email");
    }
}
