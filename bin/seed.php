#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\Env;
use App\Database\Connection;

require __DIR__ . '/../vendor/autoload.php';

Env::load(__DIR__ . '/../.env');
$config = require __DIR__ . '/../config/app.php';

$pdo = Connection::make($config['db']);

$schema = file_get_contents(__DIR__ . '/../database/schema.sql');
if ($schema === false) {
    throw new RuntimeException('Не удалось прочитать файл схемы.');
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=0');
$pdo->exec('DROP TABLE IF EXISTS category_post');
$pdo->exec('DROP TABLE IF EXISTS posts');
$pdo->exec('DROP TABLE IF EXISTS categories');
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

$pdo->exec($schema);

$categories = [
    ['name' => 'Инфраструктура', 'slug' => 'infrastructure', 'description' => 'Серверы, сети и хранение данных.'],
    ['name' => 'DevOps', 'slug' => 'devops', 'description' => 'CI/CD, контейнеры и автоматизация.'],
    ['name' => 'Безопасность', 'slug' => 'security', 'description' => 'Практики безопасной разработки и эксплуатации.'],
    ['name' => 'Наблюдаемость', 'slug' => 'observability', 'description' => 'Логи, метрики и алерты без перегруза.'],
    ['name' => 'Архитектура', 'slug' => 'architecture', 'description' => 'Дизайн систем и паттерны масштабирования.'],
    ['name' => 'Автоматизация', 'slug' => 'automation', 'description' => 'Инструменты и сценарии, которые снимают рутину.'],
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
        'image' => '/assets/images/posts/cloud-infra.svg',
        'views' => 150,
        'published_at' => '2025-01-10 10:00:00',
        'categories' => ['infrastructure', 'devops'],
    ],
    [
        'title' => 'Продвинутая настройка Nginx',
        'slug' => 'advanced-nginx',
        'description' => 'Сборка современного Nginx окружения в связке с PHP-FPM.',
        'content' => 'Разбираемся с worker-процессами, кешированием и безопасностью.',
        'image' => '/assets/images/posts/advanced-nginx.svg',
        'views' => 220,
        'published_at' => '2025-01-05 09:00:00',
        'categories' => ['infrastructure'],
    ],
    [
        'title' => 'CI/CD без боли',
        'slug' => 'cicd-without-pain',
        'description' => 'Как построить пайплайн доставки без ручных действий.',
        'content' => 'Используем GitHub Actions, Terraform и ChatOps.',
        'image' => '/assets/images/posts/cicd-without-pain.svg',
        'views' => 310,
        'published_at' => '2024-12-20 15:30:00',
        'categories' => ['devops'],
    ],
    [
        'title' => 'Укрепляем периметр',
        'slug' => 'security-hardening',
        'description' => 'Прикладные советы по защите Docker и Kubernetes.',
        'content' => 'От настроек ядра до политик доступа в кластере.',
        'image' => '/assets/images/posts/security-hardening.svg',
        'views' => 180,
        'published_at' => '2024-12-01 11:15:00',
        'categories' => ['security', 'devops'],
    ],
    [
        'title' => 'Observability без аллергии',
        'slug' => 'observability-labs',
        'description' => 'Как построить стек логов, метрик и трасс без лишнего шума.',
        'content' => 'Сравниваем Loki, Tempo и Prometheus и показываем единую панель.',
        'image' => '/assets/images/posts/observability-labs.svg',
        'views' => 95,
        'published_at' => '2025-01-18 08:45:00',
        'categories' => ['observability', 'devops'],
    ],
    [
        'title' => 'Сервисная сетка без боли',
        'slug' => 'service-mesh',
        'description' => 'Выбираем между Istio и Linkerd под реальные нагрузки.',
        'content' => 'Показываем паттерны маршрутизации и отслеживания запросов.',
        'image' => '/assets/images/posts/service-mesh.svg',
        'views' => 205,
        'published_at' => '2025-01-14 12:20:00',
        'categories' => ['infrastructure', 'devops'],
    ],
    [
        'title' => 'Бережливый Terraform',
        'slug' => 'terraform-modules',
        'description' => 'Каталог модулей, который не развалится через месяц.',
        'content' => 'Рассматриваем принципы версионирования, проверки и поставки.',
        'image' => '/assets/images/posts/terraform-modules.svg',
        'views' => 134,
        'published_at' => '2025-01-02 16:40:00',
        'categories' => ['devops', 'automation'],
    ],
    [
        'title' => 'SRE без бессонных ночей',
        'slug' => 'platform-sre',
        'description' => 'Что должно быть в платформенной команде, чтобы не гасить пожары.',
        'content' => 'Говорим о SLO, capacity review и ротациях on-call.',
        'image' => '/assets/images/posts/platform-sre.svg',
        'views' => 260,
        'published_at' => '2025-01-22 09:10:00',
        'categories' => ['observability', 'architecture'],
    ],
    [
        'title' => 'Архитектурный срез квартала',
        'slug' => 'architecture-review',
        'description' => 'Как проводить ревью систем без тонны презентаций.',
        'content' => 'Даём чек-лист для микросервисов, очередей и API.',
        'image' => '/assets/images/posts/architecture-review.svg',
        'views' => 175,
        'published_at' => '2024-11-18 13:00:00',
        'categories' => ['architecture'],
    ],
    [
        'title' => 'Финансовая оптимизация',
        'slug' => 'cost-optimization',
        'description' => 'Как сократить счёт за облако без ухудшения SLA.',
        'content' => 'Комбинируем Spot, Graviton и кастомные AMI.',
        'image' => '/assets/images/posts/cost-optimization.svg',
        'views' => 143,
        'published_at' => '2024-11-05 10:30:00',
        'categories' => ['infrastructure', 'architecture'],
    ],
    [
        'title' => 'Runbooks, которые открывают',
        'slug' => 'runbook-design',
        'description' => 'Документы, по которым можно реально восстановить сервис.',
        'content' => 'Говорим про шаблон, эскалации и метрики успеха.',
        'image' => '/assets/images/posts/runbook-design.svg',
        'views' => 88,
        'published_at' => '2024-10-25 17:55:00',
        'categories' => ['automation', 'observability'],
    ],
    [
        'title' => 'Инциденты без крика',
        'slug' => 'incident-stories',
        'description' => 'Формат пост-мортемов, который помогает учиться.',
        'content' => 'Разбираем реальные кейсы и как делиться выводами.',
        'image' => '/assets/images/posts/incident-stories.svg',
        'views' => 198,
        'published_at' => '2024-10-10 18:05:00',
        'categories' => ['security', 'observability'],
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
