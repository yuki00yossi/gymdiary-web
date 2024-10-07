<?php
/**
 * 認証関連APIのテストケース
 */

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rules;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

/**
 * ----------------------------------------------------------------
 * 新規会員登録API
 * ----------------------------------------------------------------
 */
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
    $response = $this->postJSON('/api/user', $userData);

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
    $response = $this->postJSON('/api/user', $userData);

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
    $response = $this->postJSON('/api/user', $userData);

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
    $response = $this->postJSON('/api/user', $userData);

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
    $response = $this->postJSON('/api/user', $userData);

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
    $response = $this->postJSON('/api/user', $userData);

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
    $response = $this->postJSON('/api/user', $userData);

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
    $response = $this->postJSON('/api/user', $userData);

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
    $response = $this->postJSON('/api/user', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // nameフィールドのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['name']);
});



/**
 * ----------------------------------------------------------------
 * ログイン（トークン発行）API
 * ----------------------------------------------------------------
 */
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
 * ----------------------------------------------------------------
 * ログアウト（トークン削除）API
 * ----------------------------------------------------------------
 */

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
