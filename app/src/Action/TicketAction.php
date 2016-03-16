<?php
namespace App\Action;

use Slim\Views\Twig;
use Slim\Router;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Criteria;
use Zend\Hydrator\ClassMethods;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


final class TicketAction
{
    private $view, $logger, $entity_manager, $router;

    public function __construct(Twig $view, LoggerInterface $logger, EntityManager $entity_manager, Router $router)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
        $this->router = $router;
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

    public function reset(Request $request, Response $response, $args)
    {
        if(isset($args['ticket_type_id']) && isset($args['number']))
        {
            $ticket = new \App\Entity\Ticket();
            $box = $this->entity_manager->getRepository('App\Entity\Box')->find(1);
            $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findOneById($args['ticket_type_id']);

            $data = [
                'displayedAt' => new \DateTime("now"),
                'number' => $args['number'],
                'ticketType' => $ticket_type,
                'box' => $box
            ];

            (new ClassMethods())->hydrate($data, $ticket);

            $this->entity_manager->persist($ticket);
            $this->entity_manager->flush();

            return $response->withStatus(301)->withHeader('Location', $this->router->pathFor('homepage'));
        }

        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findAll();

        $data = [
            'ticket_types' => $ticket_type
        ];

        $this->view->render($response, 'ticket.reset.twig', $data);
        return $response;
    }
}