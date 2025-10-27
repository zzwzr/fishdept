<?php

use Hyperf\HttpServer\Router\Router;

Router::addServer('ws', function () {
    Router::get('/ws', 'App\Controller\WebSocketController');
});