<?php
// Routes

$app->get('/', App\Action\HomeAction::class)->setName('homepage');
$app->group('/box/{ticket_type_id}/{box_id}', function(){
    $this->get('', 'App\Action\BoxAction:index')->setName('box.selected');
    $this->get('/previous', 'App\Action\BoxAction:previous')->setName('box.selected.previous');
    $this->get('/refresh', 'App\Action\BoxAction:refresh')->setName('box.selected.refresh');
    $this->get('/next', 'App\Action\BoxAction:next')->setName('box.selected.next');
});

$app->group('/ticket', function(){
    $this->get('/opened', 'App\Action\TicketAction:opened')->setName('ticket.opened');
    $this->get('/showed/{ticket_id}', 'App\Action\TicketAction:showed')->setName('ticket.showed');
    $this->get('/reset', 'App\Action\TicketAction:reset')->setName('ticket.reset');
    $this->get('/reset/{ticket_type_id}/{number}', 'App\Action\TicketAction:reset')->setName('ticket.reset');
});