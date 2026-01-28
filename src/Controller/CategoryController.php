<?php

namespace App\Controller;

class CategoryController extends BaseController
{
    public function index(): string
    {
        $slug = $_GET['slug'] ?? '';
        if ($slug === '') {
            throw new \InvalidArgumentException('Категория не найдена.');
        }

        $sort = $_GET['sort'] ?? 'date';
        $page = (int) ($_GET['page'] ?? 1);

        $data = $this->blog->getCategoryPage($slug, $sort, $page);
        $this->setBreadcrumbs([
            ['title' => 'Главная', 'url' => '/'],
            ['title' => $data['category']['name']],
        ]);

        return $this->view->render('category.tpl', $data);
    }
}
