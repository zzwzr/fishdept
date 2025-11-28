<?php

declare(strict_types=1);

use App\Controller\GameController;
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
    Router::get('count', [OnlineUserController::class, 'getCount'], ['operation' => '获取当前在线人数']);
});

Router::addGroup('/api/v1/', function(){
    Router::post('room', [RoomController::class, 'create'], ['operation' => '创建房间']);
    Router::get('room', [RoomController::class, 'index'], ['operation' => '房间列表']);
    Router::get('room/info', [RoomController::class, 'info'], ['operation' => '查看房间信息']);
    Router::post('room/join', [RoomController::class, 'join'], ['operation' => '加入房间']);
    Router::get('room/first', [RoomController::class, 'first'], ['operation' => '查找自己所在的房间信息']);

    Router::post('game/start', [GameController::class, 'start'], ['operation' => '游戏开始']);
    Router::post('game/move', [GameController::class, 'move'], ['operation' => '游戏状态检查']);
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