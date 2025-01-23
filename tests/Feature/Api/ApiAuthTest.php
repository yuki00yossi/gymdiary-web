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
use Illuminate\Support\Facades\Mail;

use App\Mail\MailVerificationCodeMail;

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
        'password' => 'SecureUiakPassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/user/signup', $userData);

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
    expect(Hash::check($userData['password'], $user->password))->toBeTrue();
});

it('fails if required fields are missing', function () {
    // 不完全なデータ
    $userData = [];

    // APIを呼び出し
    $response = $this->postJSON('/user/signup', $userData);

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
    $response = $this->postJSON('/user/signup', $userData);

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
    $response = $this->postJSON('/user/signup', $userData);

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
    $response = $this->postJSON('/user/signup', $userData);

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
    $response = $this->postJSON('/user/signup', $userData);

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
    $response = $this->postJSON('/user/signup', $userData);

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
    $response = $this->postJSON('/user/signup', $userData);

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
    $response = $this->postJSON('/user/signup', $userData);

    // ステータスコード422かを確認
    $response->assertStatus(422);

    // nameフィールドのバリデーションエラーメッセージを確認
    $response->assertJsonValidationErrors(['name']);
});

it('sent verification mail successfully.', function () {
    Mail::fake();
    $userData = [
        'name' => 'test',
        'email' => 'testuser@example.com',
        'username' => 'testuser',
        'password' => 'SecureUiakPassword123!',
    ];

    // APIを呼び出し
    $response = $this->postJSON('/user/signup', $userData);
    $user = User::where('email', $userData['email'])->get();

    // 確認メールが正しく飛んでいるか確認
    Mail::assertSent(MailVerificationCodeMail::class, function ($mail) use ($user) {
        return $mail->to[0]['address'] == $user[0]->email &&
            $mail->code == $user[0]->mail_verification_code;
    });
});

/**
 * ----------------------------------------------------------------
 * 会員有効化（メアド検証）API
 * ----------------------------------------------------------------
 */
it('verifies email successfully with valid code.', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);
    $user->generateMailVerificationCode();

    $response = $this->actingAs($user)->postJson('/api/email/verify', [
        'code' => $user->mail_verification_code,
    ]);

    $response->assertStatus(200)
             ->assertJson([
                 'email' => $user->email,
                 'msg' => 'success',
             ]);

    $this->assertTrue($user->fresh()->hasVerifiedEmail());
});

it('fails to verify email with invalid code.', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $user->generateMailVerificationCode();

    $response = $this->actingAs($user)->postJson('/api/email/verify', [
        'code' => 123456, // 無効なコード
    ]);

    $response->assertStatus(400)
             ->assertExactJson([
                 'not valid',
             ]);

    $this->assertFalse($user->fresh()->hasVerifiedEmail());
});

it('fails to verify email without providing a code.', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->postJson('/api/email/verify', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['code']);
});



/**
 * ----------------------------------------------------------------
 * ログイン（トークン発行）API
 * ----------------------------------------------------------------
 */
/**
 * ログインAPIの成功テスト
 */

/**
 * 誤ったパスワードでのログイン失敗テスト
 */

/**
 * 存在しないユーザーでのログイン失敗テスト
 */


/**
 * ----------------------------------------------------------------
 * ログアウト（トークン削除）API
 * ----------------------------------------------------------------
 */

/**
 * ログアウトのテスト
 */

/**
 * 未認証状態でのログアウトテスト
 */
