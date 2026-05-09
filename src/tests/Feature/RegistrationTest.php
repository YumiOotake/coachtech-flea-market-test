<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が入力されていない場合、バリデーションメッセージが表示される(): void
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('会員登録');

        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /** @test */
    public function メールアドレスが入力されていない場合、バリデーションメッセージが表示される(): void
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('会員登録');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /** @test */
    public function パスワードが入力されていない場合、バリデーションメッセージが表示される(): void
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('会員登録');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /** @test */
    public function パスワードが7文字以下の場合、バリデーションメッセージが表示される(): void
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('会員登録');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /** @test */
    public function パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される(): void
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('会員登録');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません']);
    }

    /** @test */
    public function 全ての項目が入力されている場合、会員情報が登録され、メール認証誘導画面に遷移される(): void
    {
        $this->get(route('register'))
            ->assertStatus(200)
            ->assertSee('会員登録');

        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);
    }
}
