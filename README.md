# laravel Vue Turorial

## 概要

Laravel 11 と Vue 3 を組み合わせたアプリケーションを作成したプロジェクトです。

## 前提条件

- Docker が使用できる状態であること

## 環境

- PHP 8.3
- Laravel 11
- Vue 3
- TailwindCSS

## 開始手順

### コンテナの作成

```bash
cd laravel-vue-tutorial
docker compose up -d
```

### AlmaLinux（PHP）コンテナに接続

```bash
docker exec -it laravel_vue_tutorial_php bash
```

### 各種ライブラリのインストール

```bash
composer install
npm install
```

### 環境設定

```bash
cp .env.example .env
php artisan key:generate
chown -R nginx. /var/www/html/laravel/storage
```

### データベース準備

```bash
php artisan migrate
```

### Vue のコンパイル

```bash
npm run build
```
