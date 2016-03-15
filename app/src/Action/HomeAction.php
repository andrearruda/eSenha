<?php
namespace App\Action;

use Slim\Views\Twig;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Criteria;
use Zend\Hydrator\ClassMethods;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class HomeAction
{
    private $view, $logger, $entity_manager;

    public function __construct(Twig $view, LoggerInterface $logger, EntityManager $entity_manager)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $box = $this->entity_manager->getRepository('App\Entity\Box')->findAll();
        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findAll();

        $data = [
            'boxs' => $box,
            'ticket_types' => $ticket_type
        ];

        $this->view->render($response, 'home.twig', $data);
        return $response;
    }
}
