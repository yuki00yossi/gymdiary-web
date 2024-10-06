<?php
/**
 * 新規会員登録APIのテストケース
 */

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rules;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

it('can register a new user successfully', function () {
    // リクエストデータ
    $userData = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'username' => 'testuser',
        'password' => 'SecurePassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード201かを確認
    $response->assertStatus(201);

    // レスポンスが正しい構造になっているかを確認
    $response->assertJsonStructure([
        'id',
        'name',
        'email',
        'username',
        'created_at',
        'updated_at',
    ]);

    // データベースに正しく保存されたかを確認
    $this->assertDatabaseHas('users', [
        'email' => 'testuser@example.com',
        'username' => 'testuser',
    ]);

    // パスワードがハッシュ化されて保存されていることを確認
    $user = User::where('email', 'testuser@example.com')->first();
    expect(Hash::check('SecurePassword123!', $user->password))->toBeTrue();
});

it('fails if required fields are missing', function () {
    // 不完全なデータ
    $userData = [];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード422かを確認（バリデーションエラー）
    $response->assertStatus(422);

    // エラーメッセージが含まれているかを確認
    $response->assertJsonValidationErrors(['name', 'email', 'username', 'password']);
});

it('fails if email is invalid', function () {
    // 無効なメールアドレス
    $userData = [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'username' => 'testuser',
        'password' => 'SecurePassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // emailフィールドのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['email']);
});

it('fails if username or email is already taken', function () {
    // 既存のユーザーを作成
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
        'username' => 'existinguser',
    ]);

    // 既存のメールアドレスとユーザー名を持つデータ
    $userData = [
        'name' => 'New User',
        'email' => 'existing@example.com',
        'username' => 'existinguser',
        'password' => 'SecurePassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // emailとusernameのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['email', 'username']);
});

it('fails if password does not meet strength requirements', function () {
    // 弱いパスワード
    $userData = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'username' => 'testuser',
        'password' => '123',  // 弱いパスワード
    ];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // passwordフィールドのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['password']);
});

it('fails if username has uppercase letters', function () {
    // 大文字を含むusername
    $userData = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'username' => 'TestUser',  // 大文字のあるusername
        'password' => 'SecurePassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // usernameフィールドのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['username']);
});

it('fails if username exceeds max length', function () {
    // usernameが長すぎる
    $userData = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'username' => str_repeat('a', 256),  // 256文字のusername
        'password' => 'SecurePassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // usernameフィールドのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['username']);
});

it('fails if email exceeds max length', function () {
    // emailが長すぎる
    $userData = [
        'name' => 'Test User',
        'email' => str_repeat('a', 246) . '@example.com',  // 256文字以上のemail
        'username' => 'testuser',
        'password' => 'SecurePassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // emailフィールドのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['email']);
});

it('fails if name exceeds max length', function () {
    // nameが長すぎる
    $userData = [
        'name' => str_repeat('a', 256),  // 256文字のname
        'email' => 'testuser@example.com',
        'username' => 'testuser',
        'password' => 'SecurePassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/api/register', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // nameフィールドのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['name']);
});
