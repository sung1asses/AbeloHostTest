<?php

namespace App\Model;

class Category
{
    public function __construct(
        private int $id,
        private string $name,
        private string $slug,
        private ?string $description = null
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id'],
            $row['name'],
            $row['slug'],
            $row['description'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ];
    }
}
