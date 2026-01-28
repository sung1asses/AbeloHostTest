<?php

namespace App\Controller;

class PostController extends BaseController
{
    public function show(): string
    {
        $slug = $_GET['slug'] ?? '';
        if ($slug === '') {
            throw new \InvalidArgumentException('Статья не найдена.');
        }

        $data = $this->blog->getPostPage($slug);
        $crumbs = [
            ['title' => 'Главная', 'url' => '/'],
        ];

        $primaryCategory = $data['post']['categories'][0] ?? null;
        if ($primaryCategory) {
            $crumbs[] = [
                'title' => $primaryCategory['name'],
                'url' => '/category?slug=' . $primaryCategory['slug'],
            ];
        }

        $crumbs[] = ['title' => $data['post']['title']];
        $this->setBreadcrumbs($crumbs);

        return $this->view->render('post.tpl', $data);
    }
}
