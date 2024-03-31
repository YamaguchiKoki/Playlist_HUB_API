<?php

namespace Database\Factories;

use App\Domain\Models\Post;
use App\Domain\Models\Song;
use App\Domain\Models\Tag;
use App\Domain\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Models\Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
        ];
    }
}
