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
        $ticket_types = $this->entity_manager->getRepository('App\Entity\TicketType')->findAll();

        $data = [
            'boxs' => $box,
            'ticket_types' => [
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

            $data['ticket_types']['entity'][$key] = (new ClassMethods())->extract($ticket_type);
            $data['ticket_types']['entity'][$key]['number_next'] = $number;
            unset($data['ticket_types']['entity'][$key]['created_at'], $data['ticket_types']['entity'][$key]['updated_at'], $data['ticket_types']['entity'][$key]['deleted_at']);
        }

        $this->view->render($response, 'home.twig', $data);
        return $response;
    }
}
