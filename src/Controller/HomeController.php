<?php

namespace App\Controller;

class HomeController extends BaseController
{
    public function index(): string
    {
        $categories = $this->blog->getHomeCategories();
        $this->setBreadcrumbs([
            ['title' => 'Главная'],
        ]);

        return $this->view->render('home.tpl', [
            'categories' => $categories,
        ]);
    }
}
