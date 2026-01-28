<?php

namespace App\Model;

class Post
{
    /** @param array<int, Category> $categories */
    public function __construct(
        private int $id,
        private string $title,
        private string $slug,
        private string $description,
        private string $content,
        private ?string $image,
        private int $views,
        private string $publishedAt,
        private array $categories = []
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function getPublishedAt(): string
    {
        return $this->publishedAt;
    }

    /** @return array<int, Category> */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /** @param array<int, Category> $categories */
    public function withCategories(array $categories): self
    {
        $clone = clone $this;
        $clone->categories = $categories;

        return $clone;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id'],
            $row['title'],
            $row['slug'],
            $row['description'],
            $row['content'],
            $row['image'] ?? null,
            (int) $row['views'],
            $row['published_at'],
            []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image,
            'views' => $this->views,
            'published_at' => $this->publishedAt,
            'categories' => array_map(static fn (Category $category): array => $category->toArray(), $this->categories),
        ];
    }
}
