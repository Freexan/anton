<?php

use app\controllers\ApiExampleController;
use app\controllers\ItemsController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/**
 * @var Router $router
 * @var Engine $app
 */

$router->group('', function(Router $router) use ($app) {
	$router->get('/', function() use ($app) {
		$app->render('welcome', [ 'message' => 'You are gonna do great things!' ]);
	});

	$router->get('/hello-world/@name', function($name) {
		echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	});

	$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
	$router->get('/users/@id:[0-9]+', [ ApiExampleController::class, 'getUser' ]);
	$router->post('/users/@id:[0-9]+', [ ApiExampleController::class, 'updateUser' ]);

	$router->get('/entities', [ ItemsController::class, 'index' ]);
	$router->get('/entities/@id:[0-9]+', [ ItemsController::class, 'show' ]);
	$router->post('/entities', [ ItemsController::class, 'create' ]);
	$router->put('/entities/@id:[0-9]+', [ ItemsController::class, 'update' ]);
	$router->delete('/entities/@id:[0-9]+', [ ItemsController::class, 'delete' ]);

	$router->get('/items', [ ItemsController::class, 'page' ]);

}, [ SecurityHeadersMiddleware::class ]);
