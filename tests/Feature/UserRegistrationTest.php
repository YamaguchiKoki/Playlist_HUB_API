<?php

namespace Tests\Feature;

use App\Domain\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Repository\UserRepository;
use App\Exceptions\UniqueConstraintException;
use App\Http\Responders\UserResponder;
use App\Mail\AuthCodeMailable;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $userRepository;
    protected $responder;

    /**
     * 1:初期登録(success)　メール送信できているか、レスポンスメッセージ合ってるか,DB登録できているか
     * ２：認証コード再送 メール送信できているか、レスポンスメッセージ合ってるか
     * ３：本登録済みアドレスで登録→例外
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function 初回新規ユーザー仮登録()
    {
        //準備
        Mail::fake();
        $attributes = ['email' => 'test@example.com', 'password' => 'password'];
        $expected = [
            'message' => '登録されたメールアドレス宛に認証コードを送信しました'
        ];

        //実行
        $response = $this->postJson('/api/users/register', $attributes);

        //検証
        $response->assertStatus(201);
        $response->assertJson($expected);

        Mail::assertSent(AuthCodeMailable::class, 1);
        Mail::assertSent(fn (Mailable $mailable) => $mailable->hasTo('test@example.com'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'status' => 0
        ]);
    }

    #[Test]
    public function 仮登録済みユーザーへの認証コード再送()
    {
        Mail::fake();
        $user = User::factory()->create();

        $expected = [
            'message' => '登録されたメールアドレス宛に認証コードを送信しました'
        ];

        $attributes = ['email' => $user->email, 'password' => 'password'];

        $response = $this->postJson('/api/users/register', $attributes);

        $response->assertStatus(201);
        $response->assertJson($expected);

        Mail::assertSent(AuthCodeMailable::class, 1);
        Mail::assertSent(fn (Mailable $mailable) => $mailable->hasTo($user->email));

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'status' => 0
        ]);
    }

    #[Test]
    public function 本登録済みメールアドレスは否認()
    {
        $user = User::factory()->create(['status' => 1]);

        $expected = [
            'error' => 'このメールアドレスはすでに存在しています'
        ];

        $attributes = ['email' => $user->email, 'password' => $user->password];

        $response = $this->postJson('/api/users/register', $attributes);

        $response->assertStatus(409);
        $response->assertJson($expected);
    }

    #[Test]
    public function 本登録完了()
    {
        $token = 123456;
        $email = 'email_for_test';
        $pass = 'password_for_test';
        $tommorrow = CarbonImmutable::now()->addDay();

        //仮登録済み状態のユーザーをインサート
        $user = User::factory()
                    ->create([
                        'email' => $email,
                        'password' => $pass,
                        'onetime_token' => $token,
                        'onetime_expiration' => $tommorrow,
                        'status' => 0
                    ]);

        $attributes = ['authCode' => $token];

        //上で登録したワンタイムトークンをポスト
        $response = $this->postJson('api/users/activate', $attributes);
        $response->assertStatus(201);
        //DB登録チェックステータス１になってるか
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'status' => 1
        ]);
    }

    #[Test]
    public function グーグルOAuthリダイレクトURI取得()
    {
        $response = $this->get('api/auth/redirect');

        $this->assertThat($response->content(), $this->isJson());
        $response->assertJsonStructure([
            'redirect_url'
        ]);
    }

    #[Test]
    public function グーグル登録完了()
    {
        $response = $this->get('api/auth/callback');
        $this->assertThat($response->content(), $this->isJson());
    }
}
