<?php

namespace App\Repository;

use App\Model\Category;
use App\Model\Post;
use PDO;

class PostRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findBySlug(string $slug): ?Post
    {
        $sql = 'SELECT * FROM posts WHERE slug = :slug LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch();

        if (!$row) {
            return null;
        }

        $post = Post::fromArray($row);
        $categories = $this->getPostCategories((int) $row['id']);

        return $post->withCategories($categories);
    }

    public function incrementViews(int $postId): void
    {
        $sql = 'UPDATE posts SET views = views + 1 WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $postId]);
    }

    /**
     * @param array<int, int> $categoryIds
     * @return array<int, array{title:string,slug:string,description:string,published_at:string}>
     */
    public function getRelatedPosts(int $postId, array $categoryIds, int $limit = 3): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $sql = <<<SQL
        SELECT DISTINCT p.id, p.title, p.slug, p.description, p.published_at
        FROM posts p
        INNER JOIN category_post cp ON cp.post_id = p.id
        WHERE cp.category_id IN ({$placeholders})
          AND p.id <> ?
        ORDER BY p.published_at DESC
        LIMIT ?
        SQL;

        $statement = $this->pdo->prepare($sql);

        $position = 1;
        foreach ($categoryIds as $categoryId) {
            $statement->bindValue($position, $categoryId, PDO::PARAM_INT);
            $position++;
        }

        $statement->bindValue($position, $postId, PDO::PARAM_INT);
        $statement->bindValue($position + 1, $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /** @return array<int, Category> */
    private function getPostCategories(int $postId): array
    {
        $sql = <<<SQL
        SELECT c.*
        FROM categories c
        INNER JOIN category_post cp ON cp.category_id = c.id
        WHERE cp.post_id = :postId
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['postId' => $postId]);

        $categories = [];
        while ($row = $statement->fetch()) {
            $categories[] = Category::fromArray($row);
        }

        return $categories;
    }
}
