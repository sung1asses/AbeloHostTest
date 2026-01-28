<?php

namespace App\Controller;

use App\Core\View;
use App\Service\BlogService;

abstract class BaseController
{
    protected array $breadcrumbs = [];

    public function __construct(protected View $view, protected BlogService $blog)
    {
    }

    protected function setBreadcrumbs(array $items): void
    {
        $this->breadcrumbs = $items;
        $this->view->breadcrumbs($items);
    }
}
