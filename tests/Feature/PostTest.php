<?php

namespace Tests\Feature;

use App\Domain\Models\Post;
use App\Domain\Models\Song;
use App\Domain\Models\Tag;
use App\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function 投稿新規登録成功()
    {
        //準備
        //userの登録
        $user = User::factory()->create();

        //tagの追加
        $tags = Tag::factory(3)->create();

        $tagsId = $tags->pluck('id')->toArray();
        //requestの作成
        $requestBody = [
            'post' => ['name' => 'name1', 'description' => 'desc'],
            'songs' => [
                [
                    'title' => 'title1',
                    'artist' => 'artist1',
                    'url' => 'https://example.com/song1',
                    'platform' => 0
                ],
                [
                    'title' => 'title2',
                    'artist' => 'artist2',
                    'url' => 'https://example.com/song2',
                    'platform' => 1
                ],
                [
                    'title' => 'title3',
                    'artist' => 'artist3',
                    'url' => 'https://example.com/song3',
                    'platform' => 2
                ]
            ],
            'tags' => $tagsId
        ];

        $response = $this->actingAs($user)->postJson('api/posts/register', $requestBody);
        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'name' => 'name1'
        ]);
        $this->assertDatabaseHas('post_tags', [
            'tag_id' => $tagsId[0]
        ]);
        $this->assertCount(3, Song::all());
        $this->assertThat($response->content(), $this->isJson());
    }

    #[Test]
    public function 任意のユーザーの投稿全取得()
    {
        //user->post->songの順でダミーデータ挿入
        //user挿入
        $user = User::factory()->create();

        //post挿入
        $post = Post::factory()->create(['user_id' => $user->id]);

        //song挿入
        $song = Song::factory(5)->create(['post_id' => $post->id]);

        $count = Song::count();

        $this->assertEquals(5, $count, '個数が違う');

        $response = $this->get("api/users/mypage?userId={$user->id}");

        $response->assertJsonPath('_embedded.user.id', $user->id);

        // $response->assertJsonPath('_embedded.user._embedded.posts.[0].id', $post->id);

        // $response->assertJsonPath('_embedded.user._embedded.posts._embedded.songs.id', $song->id);

        // $response->dump();
    }

    #[Test]
    public function 既存の曲の内容更新()
    {
        //準備
        //Userそうにゅう
        $user = User::factory()->create();
        //Post挿入
        $post = Post::factory()->create(['user_id' => $user->id]);
        //Song挿入
        $song = Song::factory(3)->create(['post_id' => $post->id]);

        $songIds = $song->pluck('id')->toArray();

        //UpdateSongsに、上で登録したsongのId格納してリクエスト送信
        $requestBody = [
            'updateSongs' => [
                [
                    'id' => $songIds[0],
                    'title' => 'editedTitle1',
                    'url' => 'editedUrl1',
                    'platform' => 0
                ],
                [
                    'id' => $songIds[1],
                    'title' => 'editedTitle2',
                    'url' => 'editedUrl2',
                    'platform' => 1
                ],
                [
                    'id' => $songIds[2],
                    'title' => 'editedTitle3',
                    'url' => 'editedUrl3',
                    'platform' => 2
                ],
            ]
        ];

        //更新内容が反映されているかのチェック
        $response = $this->actingAs($user)->postJson("api/posts/update/{$post->id}", $requestBody);
        $this->assertDatabaseHas('songs',[
            'title' => 'editedTitle1',
            'url' => 'editedUrl1',
            'platform' => 0
        ]);
        $response->assertStatus(200);
    }

    #[Test]
    public function 既存の曲削除()
    {
        //準備
        //Userそうにゅう
        $user = User::factory()->create();
        //Post挿入
        $post = Post::factory()->create(['user_id' => $user->id]);
        //Song挿入
        $song = Song::factory(3)->create(['post_id' => $post->id]);

        $songIds = $song->pluck('id')->toArray();

        $requestBody = [
            'deletedSongIds' => $songIds
        ];

        $response = $this->actingAs($user)->postJson("api/posts/update/{$post->id}", $requestBody);

        $this->assertDatabaseMissing('songs', [
            'id' => $songIds[0]
        ]);
    }

    #[Test]
    public function 新曲追加()
    {
        //User挿入
        $user = User::factory()->create();
        //Post挿入
        $post = Post::factory()->create(['user_id' => $user->id]);
        //Song挿入
        $song = Song::factory(3)->create(['post_id' => $post->id]);

        $requestBody = [
            'newSongs' => [
                [
                    'title' => 'newTitle',
                    'artist' => 'artist',
                    'url' => 'url',
                    'platform' =>  1
                ]
            ]
        ];

        $response = $this->actingAs($user)->postJson("api/posts/update/{$post->id}", $requestBody);

        $this->assertDatabaseHas('songs', [
            'title' => 'newTitle'
        ]);
    }

    #[Test]
    public function いいね追加()
    {
        //User挿入
        $user = User::factory()->create();
        //Post挿入
        $post = Post::factory()->create(['user_id' => $user->id]);
        //Song挿入
        $song = Song::factory(3)->create(['post_id' => $post->id]);

        $response = $this->actingAs($user)->postJson("api/posts/{$post->id}/toggle-like");
        $this->assertDatabaseHas('user_likes', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
        $this->assertDatabaseHas('posts', [
            'likes_count' => 1
        ]);
        $response->assertJsonPath('liked', true);
        $response->assertStatus(200);
    }

}
