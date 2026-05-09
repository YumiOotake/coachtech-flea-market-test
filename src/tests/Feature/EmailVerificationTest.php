<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 会員登録後、認証メールが送信される(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->get(route('verification.notice'))
            ->assertStatus(200)
            ->assertSee('認証はこちらから')
            ->assertSee('href="https://mailtrap.io"', false);
    }

    /** @test */
    public function メール認証サイトのメール認証を完了すると、プロフィール設定画面に遷移する(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash'=>sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);
        $response->assertRedirect(route('mypage.edit'));
    }
}
