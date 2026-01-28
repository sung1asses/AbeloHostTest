<?php

namespace App\Core;

use Smarty;

class View
{
    private Smarty $engine;

    public function __construct(array $paths)
    {
        $this->engine = new Smarty();
        $this->engine->setTemplateDir($paths['templates']);
        $this->engine->setCompileDir($paths['compile']);
        $this->engine->setCacheDir($paths['cache']);
    }

    public function share(string $key, mixed $value): void
    {
        $this->engine->assign($key, $value);
    }

    public function breadcrumbs(array $items): void
    {
        $this->engine->assign('breadcrumbs', $items);
    }

    public function render(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->engine->assign($key, $value);
        }

        return $this->engine->fetch($template);
    }

    public function display(string $template, array $data = []): void
    {
        echo $this->render($template, $data);
    }
}
