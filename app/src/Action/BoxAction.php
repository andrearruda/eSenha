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

final class BoxAction
{
    private $view, $logger, $entity_manager, $router;

    public function __construct(Twig $view, LoggerInterface $logger, EntityManager $entity_manager, Router $router)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
        $this->router = $router;
    }

    public function index(Request $request, Response $response, $args)
    {
        $box = $this->entity_manager->getRepository('App\Entity\Box')->findOneById($args['box_id']);
        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findOneById($args['ticket_type_id']);
        $ticket_last_five = $this->entity_manager->getRepository('App\Entity\Ticket')->lastFiveTicketBox($box, $ticket_type);

        $data = [
            'box' => $box,
            'ticket_type' => $ticket_type,
            'ticket_last_five' => $ticket_last_five
        ];

        $this->view->render($response, 'box.index.twig', $data);
        return $response;

    }

    public function previous(Request $request, Response $response, $args)
    {
        $box = $this->entity_manager->getRepository('App\Entity\Box')->findOneById($args['box_id']);
        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findOneById($args['ticket_type_id']);
        $ticket_last = $this->entity_manager->getRepository('App\Entity\Ticket')->lastTicketType($ticket_type);

        $this->disableTickets($box, $ticket_type);

        $number = empty($ticket_last) ? 0 : $ticket_last->getNumber();

        $data = [
            'box' => $box,
            'ticketType' => $ticket_type,
            'number' => $number - 1
        ];

        $entity = (new \App\Entity\Ticket());

        (new ClassMethods())->hydrate($data, $entity);

        $this->entity_manager->persist($entity);
        $this->entity_manager->flush();

        return $response->withStatus(301)->withHeader('Location', $this->router->pathFor('box.selected', ['ticket_type_id' => $args['ticket_type_id'], 'box_id' => $args['box_id']]));
    }

    public function refresh(Request $request, Response $response, $args)
    {
        $box = $this->entity_manager->getRepository('App\Entity\Box')->findOneById($args['box_id']);
        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findOneById($args['ticket_type_id']);
        $ticket_last = $this->entity_manager->getRepository('App\Entity\Ticket')->lastTicketType($ticket_type);

        $this->disableTickets($box, $ticket_type);

        $number = empty($ticket_last) ? 0 : $ticket_last->getNumber();

        $data = [
            'box' => $box,
            'ticketType' => $ticket_type,
            'number' => $number
        ];

        $entity = (new \App\Entity\Ticket());

        (new ClassMethods())->hydrate($data, $entity);

        $this->entity_manager->persist($entity);
        $this->entity_manager->flush();

        return $response->withStatus(301)->withHeader('Location', $this->router->pathFor('box.selected', ['ticket_type_id' => $args['ticket_type_id'], 'box_id' => $args['box_id']]));
    }

    public function next(Request $request, Response $response, $args)
    {
        $box = $this->entity_manager->getRepository('App\Entity\Box')->findOneById($args['box_id']);
        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findOneById($args['ticket_type_id']);
        $ticket_last = $this->entity_manager->getRepository('App\Entity\Ticket')->lastTicketType($ticket_type);

        $this->disableTickets($box, $ticket_type);

        $number = empty($ticket_last) ? 0 : $ticket_last->getNumber();

        $data = [
            'box' => $box,
            'ticketType' => $ticket_type,
            'number' => $number + 1
        ];

        $entity = (new \App\Entity\Ticket());

        (new ClassMethods())->hydrate($data, $entity);

        $this->entity_manager->persist($entity);
        $this->entity_manager->flush();

        return $response->withStatus(301)->withHeader('Location', $this->router->pathFor('box.selected', ['ticket_type_id' => $args['ticket_type_id'], 'box_id' => $args['box_id']]));
    }

    private function disableTickets(\App\Entity\Box $box, \App\Entity\TicketType $ticket_type)
    {
        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->eq('box', $box))
            ->andWhere(Criteria::expr()->eq('ticketType', $ticket_type))
            ->andWhere(Criteria::expr()->eq('displayedAt', null));

        $entities = $this->entity_manager->getRepository('App\Entity\Ticket')->matching($criteria);

        foreach($entities as $entity)
        {
            $entity->setDisplayedAt(new \DateTime("now"));
            $this->entity_manager->persist($entity);
        }

        $this->entity_manager->flush();
    }
}