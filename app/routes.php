<?php
// Routes

$app->get('/', App\Action\HomeAction::class)->setName('homepage');
$app->get('/box/info/{id}', 'App\Action\BoxAction:info')->setName('box.info');
$app->post('/box/next/{id}', 'App\Action\BoxAction:next')->setName('box.next');
$app->post('/box/previous/{id}', 'App\Action\BoxAction:previous')->setName('box.previous');
$app->post('/box/refresh/{id}', 'App\Action\BoxAction:refresh')->setName('box.refresh');