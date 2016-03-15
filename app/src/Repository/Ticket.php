<?php

namespace App\Repository;

use Doctrine\Common\Collections\Criteria;

/**
 * Ticket
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Ticket extends \Doctrine\ORM\EntityRepository
{
    public function lastFiveTicketBox(\App\Entity\Box $box, \App\Entity\TicketType $ticket_type)
    {
        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->eq('box', $box))
            ->andWhere(Criteria::expr()->eq('ticketType', $ticket_type))
            ->setMaxResults(5)
            ->orderBy(array('createdAt' => Criteria::DESC));

        return $this->_em->getRepository('App\Entity\Ticket')->matching($criteria);
    }

    public function lastTicketType(\App\Entity\TicketType $ticket_type)
    {
        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->eq('ticketType', $ticket_type))
            ->setMaxResults(1)
            ->orderBy(array('createdAt' => Criteria::DESC));

        return $this->_em->getRepository('App\Entity\Ticket')->matching($criteria)->first();
    }

    public function notDisplayed()
    {
        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->eq('displayedAt', null));

        return $this->_em->getRepository('App\Entity\Ticket')->matching($criteria);
    }

}