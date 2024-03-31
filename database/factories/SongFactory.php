<?php

namespace Database\Factories;

use App\Domain\Models\Post;
use App\Domain\Models\Song;
use App\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Models\Song>
 */
class SongFactory extends Factory
{
    protected $model = Song::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'artist' => $this->faker->name,
            'url' => $this->faker->url,
            'post_id' => Post::factory(),
            'platform' => rand(0, 4)
        ];
    }
}
