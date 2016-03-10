<?php
namespace App\Action;

use Slim\Views\Twig;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Criteria;
use Zend\Hydrator\ClassMethods;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class BoxAction
{
    private $view, $logger, $entity_manager;

    public function __construct(Twig $view, LoggerInterface $logger, EntityManager $entity_manager)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
    }

    public function info(Request $request, Response $response, $args)
    {
        $box = $this->entity_manager->getRepository('App\Entity\Box')->findOneById($args['id']);
        $tickets = $this->entity_manager->getRepository('App\Entity\Ticket')->lastFiveTicket($box);
        $ticket_types = $this->entity_manager->getRepository('App\Entity\TicketType')->findAll();

        $data = [
            'box' => (new ClassMethods())->extract($box),
            'ticket_type' => [
                'cols' => floor(12 / count($ticket_types))
            ]
        ];

        foreach($ticket_types as $key => $ticket_type)
        {
            $ticket_last = $this->entity_manager->getRepository('App\Entity\Ticket')->lastTicketType($ticket_type);

            if($ticket_last)
            {
                $number = $ticket_last->getNumber() + 1;
            }
            else
            {
                $number = 1;
            }

            $data['ticket_type']['entity'][$key] = (new ClassMethods())->extract($ticket_type);
            $data['ticket_type']['entity'][$key]['number_next'] = $number;
            unset($data['ticket_type']['entity'][$key]['created_at'], $data['ticket_type']['entity'][$key]['updated_at'], $data['ticket_type']['entity'][$key]['deleted_at']);
        }

        unset($data['box']['created_at'], $data['box']['updated_at'], $data['box']['deleted_at']);

        foreach($tickets as $key => $ticket)
        {
            $data['tickets'][$key] = (new ClassMethods())->extract($ticket);
            $data['tickets'][$key]['ticket_type'] = (new ClassMethods())->extract($ticket->getTicketType());
            unset($data['tickets'][$key]['deleted_at'], $data['tickets'][$key]['box']);
            unset($data['tickets'][$key]['ticket_type']['created_at'], $data['tickets'][$key]['ticket_type']['updated_at'], $data['tickets'][$key]['ticket_type']['deleted_at']);
        }

        return $response->withJson($data)->withHeader('Content-Type', 'application/json');
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

    public function next(Request $request, Response $response, $args)
    {
        $ticket_type_id = $request->getParam('ticket_type');
        $box_id = $args['id'];

        $box = $this->entity_manager->getRepository('App\Entity\Box')->findOneById($box_id);
        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findOneById($ticket_type_id);
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

        $tickets = $this->entity_manager->getRepository('App\Entity\Ticket')->lastFiveTicket($entity->getBox());

        $data = [
            'box' => (new ClassMethods())->extract($entity),
        ];

        unset($data['box']['created_at'], $data['box']['updated_at'], $data['box']['deleted_at']);

        foreach($tickets as $key => $ticket)
        {
            $data['tickets'][$key] = (new ClassMethods())->extract($ticket);
            $data['tickets'][$key]['ticket_type'] = (new ClassMethods())->extract($ticket->getTicketType());
            unset($data['tickets'][$key]['deleted_at'], $data['tickets'][$key]['box']);

            unset($data['tickets'][$key]['ticket_type']['created_at'], $data['tickets'][$key]['ticket_type']['updated_at'], $data['tickets'][$key]['ticket_type']['deleted_at']);
        }

        return $response->withJson($data)->withHeader('Content-Type', 'application/json');
    }

    public function previous(Request $request, Response $response, $args)
    {
        $ticket_type_id = $request->getParam('ticket_type');
        $box_id = $args['id'];

        $box = $this->entity_manager->getRepository('App\Entity\Box')->findOneById($box_id);
        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findOneById($ticket_type_id);
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

        $tickets = $this->entity_manager->getRepository('App\Entity\Ticket')->lastFiveTicket($entity->getBox());

        $data = [
            'box' => (new ClassMethods())->extract($entity),
        ];

        unset($data['box']['created_at'], $data['box']['updated_at'], $data['box']['deleted_at']);

        foreach($tickets as $key => $ticket)
        {
            $data['tickets'][$key] = (new ClassMethods())->extract($ticket);
            $data['tickets'][$key]['ticket_type'] = (new ClassMethods())->extract($ticket->getTicketType());
            unset($data['tickets'][$key]['deleted_at'], $data['tickets'][$key]['box']);

            unset($data['tickets'][$key]['ticket_type']['created_at'], $data['tickets'][$key]['ticket_type']['updated_at'], $data['tickets'][$key]['ticket_type']['deleted_at']);
        }

        return $response->withJson($data)->withHeader('Content-Type', 'application/json');
    }

    public function refresh(Request $request, Response $response, $args)
    {
        $ticket_type_id = $request->getParam('ticket_type');
        $box_id = $args['id'];

        $box = $this->entity_manager->getRepository('App\Entity\Box')->findOneById($box_id);
        $ticket_type = $this->entity_manager->getRepository('App\Entity\TicketType')->findOneById($ticket_type_id);

        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->eq('box', $box))
            ->andWhere(Criteria::expr()->eq('ticketType', $ticket_type))
            ->orderBy(array('createdAt' => Criteria::DESC));

        $ticket_last = $this->entity_manager->getRepository('App\Entity\Ticket')->matching($criteria)->first();

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

        $tickets = $this->entity_manager->getRepository('App\Entity\Ticket')->lastFiveTicket($entity->getBox());

        $data = [
            'box' => (new ClassMethods())->extract($entity),
        ];

        unset($data['box']['created_at'], $data['box']['updated_at'], $data['box']['deleted_at']);

        foreach($tickets as $key => $ticket)
        {
            $data['tickets'][$key] = (new ClassMethods())->extract($ticket);
            $data['tickets'][$key]['ticket_type'] = (new ClassMethods())->extract($ticket->getTicketType());
            unset($data['tickets'][$key]['deleted_at'], $data['tickets'][$key]['box']);

            unset($data['tickets'][$key]['ticket_type']['created_at'], $data['tickets'][$key]['ticket_type']['updated_at'], $data['tickets'][$key]['ticket_type']['deleted_at']);
        }

        return $response->withJson($data)->withHeader('Content-Type', 'application/json');
    }
}