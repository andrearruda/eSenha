<?php
namespace App\Action;

use Slim\Views\Twig;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Criteria;
use Zend\Hydrator\ClassMethods;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


final class TicketAction
{
    private $view, $logger, $entity_manager;

    public function __construct(Twig $view, LoggerInterface $logger, EntityManager $entity_manager)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
    }

    public function opened(Request $request, Response $response, $args)
    {
        $data = $this->entity_manager->getRepository('App\Entity\Ticket')->notDisplayed()->toArray();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $data = $serializer->serialize($data, 'xml');

        $body = $response->getBody();
        $body->write($data);

        return $response->withBody($body)->withHeader('Content-type', 'application/xml');
    }

    public function showed(Request $request, Response $response, $args)
    {
        $ticket = $this->entity_manager->getRepository('App\Entity\Ticket')->findOneById($args['ticket_id']);
        $ticket->setDisplayedAt(new \DateTime("now"));

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $data = $serializer->serialize($ticket, 'xml');

        $this->entity_manager->persist($ticket);
        $this->entity_manager->flush();

        $body = $response->getBody();
        $body->write($data);

        return $response->withBody($body)->withHeader('Content-type', 'application/xml');
    }
}