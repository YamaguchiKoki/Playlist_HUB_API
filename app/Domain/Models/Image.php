<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Image extends Model
{
    use HasFactory, Notifiable;

    public function imageable()
    {
        return $this->morphTo();
    }
}
