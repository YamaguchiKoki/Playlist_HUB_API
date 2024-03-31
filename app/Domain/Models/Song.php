<?php

namespace App\Domain\Models;

use App\Enums\MusicPlatform;
use Database\Factories\SongFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Song extends Model
{
    use HasFactory, Notifiable;


    protected $guarded = [];

    protected $attributes = [
        'platform' => MusicPlatform::class,
    ];

    protected static function newFactory()
    {
        return SongFactory::new();
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
