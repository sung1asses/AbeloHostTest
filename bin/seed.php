#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\Env;
use App\Database\Connection;
use RuntimeException;

require __DIR__ . '/../vendor/autoload.php';

Env::load(__DIR__ . '/../.env');
$config = require __DIR__ . '/../config/app.php';

$pdo = Connection::make($config['db']);

$schema = file_get_contents(__DIR__ . '/../database/schema.sql');
if ($schema === false) {
    throw new RuntimeException('Не удалось прочитать файл схемы.');
}

$pdo->exec($schema);

$pdo->exec('SET FOREIGN_KEY_CHECKS=0');
$pdo->exec('TRUNCATE TABLE category_post');
$pdo->exec('TRUNCATE TABLE posts');
$pdo->exec('TRUNCATE TABLE categories');
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

$categories = [
    ['name' => 'Инфраструктура', 'slug' => 'infrastructure', 'description' => 'Серверы, сети и хранение данных.'],
    ['name' => 'DevOps', 'slug' => 'devops', 'description' => 'CI/CD, контейнеры и автоматизация.'],
    ['name' => 'Безопасность', 'slug' => 'security', 'description' => 'Практики безопасной разработки и эксплуатации.'],
];

$categoryIds = [];
$insertCategory = $pdo->prepare('INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)');

foreach ($categories as $category) {
    $insertCategory->execute($category);
    $categoryIds[$category['slug']] = (int) $pdo->lastInsertId();
}

$posts = [
    [
        'title' => 'Запуск инфраструктуры в облаке',
        'slug' => 'cloud-infra',
        'description' => 'Пошаговый план переноса инфраструктуры в Kubernetes кластер.',
        'content' => 'Подробно разбираем подход к проектированию архитектуры и автоматизации.',
        'image' => null,
        'views' => 150,
        'published_at' => '2025-01-10 10:00:00',
        'categories' => ['infrastructure', 'devops'],
    ],
    [
        'title' => 'Продвинутая настройка Nginx',
        'slug' => 'advanced-nginx',
        'description' => 'Сборка современного Nginx окружения в связке с PHP-FPM.',
        'content' => 'Разбираемся с worker-процессами, кешированием и безопасностью.',
        'image' => null,
        'views' => 220,
        'published_at' => '2025-01-05 09:00:00',
        'categories' => ['infrastructure'],
    ],
    [
        'title' => 'CI/CD без боли',
        'slug' => 'cicd-without-pain',
        'description' => 'Как построить пайплайн доставки без ручных действий.',
        'content' => 'Используем GitHub Actions, Terraform и ChatOps.',
        'image' => null,
        'views' => 310,
        'published_at' => '2024-12-20 15:30:00',
        'categories' => ['devops'],
    ],
    [
        'title' => 'Укрепляем периметр',
        'slug' => 'security-hardening',
        'description' => 'Прикладные советы по защите Docker и Kubernetes.',
        'content' => 'От настроек ядра до политик доступа в кластере.',
        'image' => null,
        'views' => 180,
        'published_at' => '2024-12-01 11:15:00',
        'categories' => ['security', 'devops'],
    ],
];

$insertPost = $pdo->prepare('INSERT INTO posts (title, slug, description, content, image, views, published_at) VALUES (:title, :slug, :description, :content, :image, :views, :published_at)');
$attach = $pdo->prepare('INSERT INTO category_post (category_id, post_id) VALUES (:category_id, :post_id)');

foreach ($posts as $post) {
    $data = $post;
    unset($data['categories']);
    $insertPost->execute($data);
    $postId = (int) $pdo->lastInsertId();

    foreach ($post['categories'] as $categorySlug) {
        $categoryId = $categoryIds[$categorySlug] ?? null;
        if ($categoryId === null) {
            continue;
        }

        $attach->execute([
            'category_id' => $categoryId,
            'post_id' => $postId,
        ]);
    }
}

echo "Готово! Категории и статьи загружены.\n";
