<?php

namespace App\Service;

use App\Model\Category;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use InvalidArgumentException;

class BlogService
{
    public function __construct(
        private CategoryRepository $categories,
        private PostRepository $posts,
        private int $perPage
    ) {
    }

    public function getHomeCategories(): array
    {
        return $this->categories->getCategoriesWithRecentPosts(3);
    }

    public function getCategoryPage(string $slug, string $sort, int $page): array
    {
        $category = $this->categories->findBySlug($slug);
        if (!$category) {
            throw new InvalidArgumentException('Категория не найдена.');
        }

        $sort = $this->normalizeSort($sort);
        $page = max(1, $page);

        $result = $this->categories->getPostsForCategory($category->getId(), $sort, $page, $this->perPage);

        $totalPages = max(1, (int) ceil($result['total'] / $this->perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
            $result = $this->categories->getPostsForCategory($category->getId(), $sort, $page, $this->perPage);
        }

        $articles = array_map(static function (array $post): array {
            return [
                'title' => $post['title'],
                'excerpt' => $post['description'],
                'slug' => $post['slug'],
                'image' => $post['image'] ?? null,
                'views' => (int) $post['views'],
                'published_at' => $post['published_at'],
            ];
        }, $result['posts']);

        return [
            'category' => $category->toArray(),
            'articles' => $articles,
            'filters' => [
                'current' => $sort,
                'options' => [
                    'date' => 'По дате публикации',
                    'views' => 'По просмотрам',
                ],
            ],
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages,
                'prev' => max(1, $page - 1),
                'next' => min($totalPages, $page + 1),
            ],
        ];
    }

    public function getPostPage(string $slug): array
    {
        $post = $this->posts->findBySlug($slug);
        if (!$post) {
            throw new InvalidArgumentException('Статья не найдена.');
        }

        $this->posts->incrementViews($post->getId());

        $categoryIds = array_map(static fn (Category $category): int => $category->getId(), $post->getCategories());
        $related = $this->posts->getRelatedPosts($post->getId(), $categoryIds, 3);

        $postData = $post->toArray();
        $postData['views']++;

        return [
            'post' => $postData,
            'related' => $related,
        ];
    }

    private function normalizeSort(string $sort): string
    {
        return in_array($sort, ['views', 'date'], true) ? $sort : 'date';
    }
}
