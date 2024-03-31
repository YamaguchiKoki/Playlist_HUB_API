<?php

namespace App\Domain\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Tag extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected static function newFactory()
    {
        return TagFactory::new();
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }
}
