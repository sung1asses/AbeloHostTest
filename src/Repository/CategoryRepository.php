<?php

namespace App\Repository;

use App\Model\Category;
use PDO;

class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array<int, array{name:string,slug:string,description:?string,articles:array<int, array{title:string,slug:string,published_at:string}>}>
     */
    public function getCategoriesWithRecentPosts(int $postsLimit = 3): array
    {
        $sql = <<<SQL
        SELECT ranked.* FROM (
            SELECT
            c.id AS category_id,
            c.name AS category_name,
                c.slug,
                c.description,
                p.id AS post_id,
                p.title AS post_title,
                p.slug AS post_slug,
                p.published_at,
                ROW_NUMBER() OVER (PARTITION BY c.id ORDER BY p.published_at DESC) AS position
            FROM categories c
            INNER JOIN category_post cp ON cp.category_id = c.id
            INNER JOIN posts p ON p.id = cp.post_id
        ) AS ranked
        WHERE ranked.position <= :postsLimit
        ORDER BY ranked.category_name, ranked.published_at DESC
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue('postsLimit', $postsLimit, PDO::PARAM_INT);
        $statement->execute();

        $grouped = [];

        while ($row = $statement->fetch()) {
            $categoryId = (int) $row['category_id'];
            if (!isset($grouped[$categoryId])) {
                $grouped[$categoryId] = [
                    'name' => $row['category_name'],
                    'slug' => $row['slug'],
                    'description' => $row['description'] ?? null,
                    'articles' => [],
                ];
            }

            $grouped[$categoryId]['articles'][] = [
                'title' => $row['post_title'],
                'slug' => $row['post_slug'],
                'published_at' => $row['published_at'],
            ];
        }

        return array_values($grouped);
    }

    public function findBySlug(string $slug): ?Category
    {
        $sql = 'SELECT * FROM categories WHERE slug = :slug LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch();

        return $row ? Category::fromArray($row) : null;
    }

    /** @return array{posts: array<int, array<string, mixed>>, total: int} */
    public function getPostsForCategory(int $categoryId, string $sort, int $page, int $perPage): array
    {
        $orderBy = $sort === 'views' ? 'p.views DESC' : 'p.published_at DESC';
        $offset = ($page - 1) * $perPage;

        $countSql = 'SELECT COUNT(*) FROM category_post WHERE category_id = :categoryId';
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute(['categoryId' => $categoryId]);
        $total = (int) $countStmt->fetchColumn();

        $sql = <<<SQL
        SELECT p.id, p.title, p.slug, p.description, p.views, p.published_at
        FROM category_post cp
        INNER JOIN posts p ON p.id = cp.post_id
        WHERE cp.category_id = :categoryId
        ORDER BY {$orderBy}
        LIMIT :limit OFFSET :offset
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue('categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue('limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $posts = $statement->fetchAll();

        return ['posts' => $posts, 'total' => $total];
    }
}
