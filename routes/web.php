<?php

use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Controller\PostController;
use App\Core\Router;
use App\Core\View;
use App\Service\BlogService;

return static function (Router $router, View $view, BlogService $blog): void {
    $home = new HomeController($view, $blog);
    $categories = new CategoryController($view, $blog);
    $posts = new PostController($view, $blog);

    $router->get('/', [$home, 'index']);
    $router->get('/category', [$categories, 'index']);
    $router->get('/post', [$posts, 'show']);
};
