<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

/**
 * ログインAPIの成功テスト
 */
it('logs in a user successfully and issues a token', function () {
    // テスト用のユーザー作成
    $user = User::factory()->create([
        'email' => 'testuser@example.com',
        'password' => Hash::make('password123'), // ハッシュされたパスワード
    ]);

    // 正しい認証情報でAPIリクエストを送信
    $response = $this->postJson('/api/token', [
        'email' => 'testuser@example.com',
        'password' => 'password123'
    ]);

    // ステータス200を期待し、トークンを含むレスポンスを確認
    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'token',
        ]);
});

/**
 * 誤ったパスワードでのログイン失敗テスト
 */
it('fails to log in with wrong password', function () {
    // テスト用のユーザー作成
    $user = User::factory()->create([
        'email' => 'testuser@example.com',
        'password' => Hash::make('password123'),
    ]);

    // 誤ったパスワードでAPIリクエストを送信
    $response = $this->postJson('/api/token', [
        'email' => 'testuser@example.com',
        'password' => 'wrongpassword'
    ]);

    // 認証失敗を確認し、ステータス401が返されることを確認
    $response->assertStatus(401)
        ->assertJson([
            'message' => 'ログイン情報が正しくありません。'
        ]);
});

/**
 * 存在しないユーザーでのログイン失敗テスト
 */
it('fails to log in with non-existent user', function () {
    // 存在しないユーザーの情報でAPIリクエストを送信
    $response = $this->postJson('/api/token', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123'
    ]);

    // 認証失敗を確認し、ステータス401が返されることを確認
    $response->assertStatus(401)
        ->assertJson([
            'message' => 'ログイン情報が正しくありません。'
        ]);
});

/**
 * バリデーションエラーテスト（必須項目の不足）
 */
it('fails to log in when email or password is missing', function () {
    // パスワードが欠けているリクエスト送信
    $response = $this->postJson('/api/token', [
        'email' => 'testuser@example.com'
    ]);

    // ステータス422を期待し、バリデーションエラーを確認
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

/**
 * ログアウトのテスト
 */
it('logs out the authenticated user successfully', function () {
    // 認証済みユーザーを作成
    $user = User::factory()->create();

    // Sanctumを利用してユーザーを認証
    Sanctum::actingAs($user);

    // ログアウトAPIリクエスト送信
    $response = $this->deleteJson('/api/token');

    // ステータス200を期待し、ログアウト成功メッセージを確認
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Deleted token successfully.'
        ]);
});

/**
 * 未認証状態でのログアウトテスト
 */
it('fails to log out when not authenticated', function () {
    // 認証なしでログアウトリクエスト送信
    $response = $this->deleteJson('/api/token');

    // 認証エラーを期待
    $response->assertStatus(401);
});
