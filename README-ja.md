# Gym Diary - Backend

Gym Diaryのバックエンドリポジトリへようこそ！このプロジェクトは、トレーナーとトレーニーをいつでもどこでもパーソナライズされたフィットネスセッションにマッチングするためのGym DiaryサービスのバックエンドAPIです。

## 概要

Gym Diaryのバックエンドは、ユーザー認証、トレーナー・トレーニーマッチング、セッションスケジューリング、進捗トラッキングなどのコア機能を処理します。スケーラビリティとセキュリティを重視して設計されており、Gym Diaryエコシステムの基盤となるAPIです。

このプロジェクトでは、ローカル開発環境のセットアップを簡素化するために、Dockerベースの環境を提供する[Laravel Sail](https://laravel.com/docs/11.x/sail)を使用しています。

## 機能

- Sanctumベースのユーザー認証
- トレーナーとトレーニーのプロフィール管理
- トレーナーとトレーニーのマッチングシステム
- セッションのスケジューリングと管理
- トレーニーの進捗トラッキング
- 複数のフィットネス目標に対応

## インストール方法

Laravel Sailを使用して、学習や開発目的でローカル環境をセットアップするために、以下の手順に従ってください。

1. リポジトリをクローンします:
    ```bash
    git clone https://github.com/yourusername/gym-diary-backend.git
    ```
2. プロジェクトディレクトリに移動します:
    ```bash
    cd gym-diary-backend
    ```
3. 依存関係をインストールします:
    ```bash
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
    ```
4. 環境変数を設定します（`.env.example`を参考にしてください）:
    ```bash
    cp .env.example .env
    ```
5. .envの内容を修正します：
   ```.env
   # DB_CONNECTION=sqlite コメントアウト
   
   # 以下のコメントアウトを外す
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=hoge
   DB_USERNAME=sail
   DB_PASSWORD=password
   ```
6. 開発環境を起動します:
    ```bash
    ./vendor/bin/sail up
    ```

7. マイグレーションを実行します:
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

8. 開発サーバーは `http://localhost` で利用可能です。

## 使用方法

開発環境が起動したら、`http://localhost` で利用できます。APIのルートに関するドキュメントは近日公開予定です。

## ライセンス

このプロジェクトはカスタムライセンスで提供されています。**個人の学習目的**でこのリポジトリをフォークして使用することは自由ですが、**商用利用や商業的な配布は事前の許可なしには厳禁**です。

### 許可されること:
- 学習や開発目的でのフォーク
- プロジェクトへのコントリビューション

### 許可されないこと:
- 商用利用（有料サービスやアプリケーションへの統合など）
- 商業的目的でのプロジェクトや変更後のバージョンの再配布

ライセンスに関するお問い合わせは、直接ご連絡ください。

## コントリビューション

コントリビューションは大歓迎です！アイデア、改善点、バグ修正などがあれば、issueを立てるかプルリクエストを送ってください。一緒に素晴らしいものを作りましょう！

1. このリポジトリをフォークします。
2. フィーチャーブランチを作成します: `git checkout -b feature/your-feature`
3. 変更をコミットします: `git commit -m 'Add your feature'`
4. ブランチにプッシュします: `git push origin feature/your-feature`
5. プルリクエストを作成します。

皆さんからのコントリビューションをお待ちしております！

## お問い合わせ

お問い合わせや質問がある場合は、yoshioka@studio-babe.jp までお気軽にご連絡ください。
