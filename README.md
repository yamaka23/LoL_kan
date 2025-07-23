# LoL_kan（LoLビルド＆対策共有アプリ）

LoL（League of Legends）プレイヤー向けの、ビルドや対策情報を投稿・閲覧できるWebアプリです。

## 🎯 制作目的

## 🎯 制作目的

LoLをプレイする中で、自分が使うチャンピオンと相手のチャンピオンとの相性が「勝率」などの数値でしか判断できず、  
**実際にどんなアイテムを積むべきか、どんなルーン構成・立ち回りが有効かが分かりにくい**と感じていました。

そこでこのアプリでは、実際の対面経験に基づいたビルド・ルーン・立ち回りなどの対策情報を、  
**ユーザー同士が投稿・閲覧・共有できる場**を提供することで、プレイヤーがより深くLoLの理解を深められることを目的としています。

## 🧠 工夫したポイント

- チャンピオン・レーンなど、**検索性を意識した投稿構成**
- 投稿に「いいね」や「コメント」ができる、**SNS的な要素の追加**
- 認証（ログイン/新規登録）や中間テーブル設計など、**Laravelの機能を幅広く活用**


## 🧩 実装機能

- [x] 投稿の一覧・詳細・作成・編集・削除（CRUD機能）
- [x] ログイン・ユーザー登録（Breeze使用）
- [x] 投稿への「いいね」機能（多対多）
- [x] チャンピオン別絞り込み表示
- [ ] コメント機能（実装予定）
- [ ] お気に入り機能（実装予定）

## 🗃 データベース構成（抜粋）

| テーブル名 | 主なカラム | 補足 |
|------------|------------|------|
| users | name| Breezeで生成 |
| posts | title, body, user_id, champion_id, lane_id | 投稿情報 |
| champions | name, image | 対象チャンピオン |
| likes | user_id, post_id | 多対多の中間テーブル |
| runes / items | name, stats | JSONカラムで管理（予定） |

## 🛠 使用技術

- Laravel 10.48.29 / PHP 8.2.14
- MySQL（MAMP環境）
- Laravel Breeze（認証）
- Git / GitHub
- VS Code
- Heroku（デプロイ予定）

## 🚀 今後の展望

- コメント機能や通知機能の追加
- チャンピオンやルーン情報を外部API連携で取得
- スマホ表示への最適化（レスポンシブ対応）

## 📝 備考

- コードの可読性と保守性を意識し、Controller・Modelの責務を分離
- マイグレーションやSeederを用いて、DB構築の再現性を担保
- コミットは意味のある粒度で行い、GitHubでの履歴管理も徹底

## 📷 アプリ画面（※スクリーンショットがあればここに貼る）

> ※ `images/` に画像を入れて、Markdownで表示できます。

## 📎 リンク

- 本番環境（Herokuなど）: 準備中
- GitHubリポジトリ: [https://github.com/yourname/LoL_kan](https://github.com/yourname/LoL_kan)
"""

readme_path = Path("/mnt/data/README_LoL_kan.md")
readme_path.write_text(readme_content, encoding="utf-8")
readme_path.name
