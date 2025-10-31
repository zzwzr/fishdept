<?php

declare(strict_types=1);

use App\Controller\LoginController;
use App\Controller\OnlineUserController;
use App\Controller\RoomController;
use App\Controller\TestController;
use App\Controller\UploadController;
use App\Controller\UserController;
use Hyperf\HttpServer\Router\Router;

Router::get('/api/v1/test', [TestController::class, 'test']);
Router::post('/api/v1/upload', [UploadController::class, 'upload']);

Router::addGroup('/api/v1/user/', function(){
    Router::post('register', [LoginController::class, 'register']);
    Router::post('login', [LoginController::class, 'login']);
});

Router::addGroup('/api/v1/online/', function(){
    Router::get('count', [OnlineUserController::class, 'getCount']);
});

Router::addGroup('/api/v1/', function(){
    Router::post('room', [RoomController::class, 'create']);
    Router::get('room', [RoomController::class, 'index']);
});

Router::addGroup('/api/v1/user/', function(){
    Router::post('users', [UserController::class, 'create']);
    Router::delete('users', [UserController::class, 'delete']);
    Router::put('users', [UserController::class, 'update']);
    Router::get('users', [UserController::class, 'index']);
    Router::get('user', [UserController::class, 'list']);
}, [
    'middleware' => [
        \App\Middleware\JwtMiddleware::class,
    ],
]);