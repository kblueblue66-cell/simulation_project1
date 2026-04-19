# coachtech フリマ
ある企業が開発した独自のフリマアプリの、出品・購入・決済機能を備えたWebサービスです。

## 機能一覧
- 会員登録・ログイン機能（Fortify使用）

- メール認証機能（応用要件：会員登録時のメール送付と認証誘導）

- 商品一覧・検索機能（部分一致検索、マイリスト表示）

- 商品詳細表示・いいね・コメント機能

- 商品購入機能（Stripe決済連携、配送先スナップショット保存）

- プロフィール管理・出品機能（画像アップロード、storage保存）

- 配送先変更機能（購入画面からの住所変更・反映）


## 環境構築
**Dockerビルド**
1. `git clone git@github.com:kblueblue66-cell/simulation_project1.git`
2. cd simulation_project1
3. DockerDesktopアプリを立ち上げる
4. `docker-compose up -d --build`

> *MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください*
``` bash
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
```bash
cp .env.example .env
```
4. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
** Mailtrap設定　**
1. Mailtrap にアクセスし、アカウント作成・ログインし、「Sandboxes」から設定情報を取得します。
2. .env の以下の項目を書き換えてください。
3. 「SMTP Settings」タブにある「Integrations」のプルダウンから 「Laravel 7+」 を選択すると、設定すべき値が表示されます。
```text
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=（MailtrapのUser Nameを入力）
MAIL_PASSWORD=（MailtrapのPasswordを入力）
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@coachtech-frima.com"
MAIL_FROM_NAME="${APP_NAME}"
```

** stripe設定　**
1. Stripe開発者アカウント: 公式サイトでアカウントを作成します。
2. テスト用APIキーの取得: ダッシュボードの「開発者」＞「APIキー」から、以下の2つのキーを取得してください。
公開可能キー (Publishable key): pk_test_...
シークレットキー (Secret key): sk_test_...
3. .envの以下の項目を書き換えて下さい。
```text
STRIPE_PUBLIC_KEY=pk_test_あなたの公開可能キー
STRIPE_SECRET_KEY=sk_test_あなたのシークレットキー
```

5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
```

8. シンボリックリンク作成
``` bash
php artisan storage:link
```
9. テストの実行
```bash
php artisan test
```

## 使用技術(実行環境)
- 言語/フレームワーク: PHP 8.1.34 / Laravel 8.83.8

- データベース: MariaDB 11.8.3 (MySQL互換)

- インフラ: Docker 28.4.0

- 認証: Laravel Fortify 1.19.1

- 決済: Stripe API

- ツール: GitHub / Mailtrap (メール認証テスト用)

## テーブル設計
<img width="757" height="189" alt="スクリーンショット 2026-04-19 13 18 58" src="https://github.com/user-attachments/assets/0eeb5e7a-d1c5-4098-bf31-a423d47586ff" />

<img width="757" height="189" alt="スクリーンショット 2026-04-19 13 19 04" src="https://github.com/user-attachments/assets/b25157cd-016c-41d5-ad0b-531b79e7a823" />

<img width="757" height="231" alt="スクリーンショット 2026-04-19 13 19 14" src="https://github.com/user-attachments/assets/193f42c3-8492-4d66-a5fe-2a3effc927a6" />

<img width="757" height="106" alt="スクリーンショット 2026-04-19 13 19 24" src="https://github.com/user-attachments/assets/bb83ddec-5a67-46a6-bf61-9a1528c569ec" />

<img width="757" height="110" alt="スクリーンショット 2026-04-19 13 19 32" src="https://github.com/user-attachments/assets/9c3b6559-c81c-4da7-8802-4055c71c6408" />

<img width="757" height="222" alt="スクリーンショット 2026-04-19 13 19 40" src="https://github.com/user-attachments/assets/e15411cd-e8ec-4de9-89da-d3b35b874470" />

<img width="757" height="124" alt="スクリーンショット 2026-04-19 13 19 49" src="https://github.com/user-attachments/assets/e7f0619a-0f55-42b0-985d-902c4ca03496" />

<img width="757" height="147" alt="スクリーンショット 2026-04-19 13 19 56" src="https://github.com/user-attachments/assets/333d4102-12e8-401b-b3ae-a25163b6237b" />

<img width="757" height="105" alt="スクリーンショット 2026-04-19 13 20 04" src="https://github.com/user-attachments/assets/40dfac2e-4f96-46c9-b181-e6136f0bfd28" />

## ER図
<img width="558" height="308" alt="スクリーンショット 2026-04-19 13 14 08" src="https://github.com/user-attachments/assets/8d22ed7b-9c11-457f-86aa-c25b21c20d32" />


## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/