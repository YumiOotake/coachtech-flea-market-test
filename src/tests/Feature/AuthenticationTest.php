<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレスが入力されていない場合、バリデーションメッセージが表示される(): void
    {
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('ログイン');

        $response = $this->post(route('login'), [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /** @test */
    public function パスワードが入力されていない場合、バリデーションメッセージが表示される(): void
    {
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('ログイン');

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /** @test */
    public function 入力情報が間違っている場合、バリデーションメッセージが表示される(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('ログイン');

        $response = $this->post(route('login'), [
            'email' => 'different-test@example.com',
            'password' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    /** @test */
    public function 正しい情報が入力された場合、ログイン処理が実行される(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->get(route('login'))
            ->assertStatus(200)
            ->assertSee('ログイン');

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('mypage.edit'));
    }

    /** @test */
    public function ログアウトができる(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('items.index'));
        $this->assertGuest();
    }
}
