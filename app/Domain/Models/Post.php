<?php

namespace App\Domain\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    protected static function newFactory()
    {
        return PostFactory::new();
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_likes');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function images()
    {
        return $this->morphMany('App/Domain/Models/Image', 'imageable');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }
}
