<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Env;
use App\Core\Logger;
use App\Core\Router;
use App\Core\View;
use App\Database\Connection;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\BlogService;

require __DIR__ . '/../vendor/autoload.php';

Env::load(__DIR__ . '/../.env');
$config = require __DIR__ . '/../config/app.php';
Config::load($config);

$view = new View($config['paths']);
$view->share('app', $config['app']);
$view->share('baseUrl', $config['app']['url']);
$view->breadcrumbs([]);

$logger = new Logger($config['paths']['logs'] . '/app.log');

$pdo = Connection::make($config['db']);
$categoryRepository = new CategoryRepository($pdo);
$postRepository = new PostRepository($pdo);
$blogService = new BlogService($categoryRepository, $postRepository, $config['pagination']['per_page']);

$router = new Router();
$routes = require __DIR__ . '/../routes/web.php';
$routes($router, $view, $blogService);

try {
	$response = $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');

	if (is_string($response)) {
		echo $response;
	} elseif ($response === null) {
		http_response_code(404);
		$view->display('error.tpl', [
			'code' => 404,
			'heading' => 'Страница не найдена',
			'message' => 'У нас нет такой страницы или она переехала.',
		]);
	}
} catch (\InvalidArgumentException $exception) {
	$logger->warning('Invalid argument exception', [
		'error' => $exception->getMessage(),
		'path' => $_SERVER['REQUEST_URI'] ?? '',
	]);
	http_response_code(404);
	$view->display('error.tpl', [
		'code' => 404,
		'heading' => 'Страница не найдена',
		'message' => $exception->getMessage(),
	]);
} catch (\Throwable $exception) {
	$logger->error('Unhandled exception', [
		'error' => $exception->getMessage(),
		'path' => $_SERVER['REQUEST_URI'] ?? '',
	]);
	http_response_code(500);
	$message = Config::get('app.env') === 'local' && Config::get('app.debug', false)
		? $exception->getMessage()
		: 'Что-то пошло не так.';

	$view->display('error.tpl', [
		'code' => 500,
		'heading' => 'Сбой сервера',
		'message' => $message,
	]);
}
