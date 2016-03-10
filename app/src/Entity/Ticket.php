<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket", indexes={@ORM\Index(name="fk_ticket_ticket_type_idx", columns={"ticket_type_id"}), @ORM\Index(name="fk_ticket_box1_idx", columns={"box_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\Ticket")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Ticket
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $number;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="displayed_at", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $displayedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $deletedAt;

    /**
     * @var \App\Entity\TicketType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TicketType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ticket_type_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $ticketType;

    /**
     * @var \App\Entity\Box
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Box")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="box_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $box;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Ticket
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set displayedAt
     *
     * @param \DateTime $displayedAt
     *
     * @return Ticket
     */
    public function setDisplayedAt($displayedAt)
    {
        $this->displayedAt = $displayedAt;

        return $this;
    }

    /**
     * Get displayedAt
     *
     * @return \DateTime
     */
    public function getDisplayedAt()
    {
        return $this->displayedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Ticket
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Ticket
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Ticket
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set ticketType
     *
     * @param \App\Entity\TicketType $ticketType
     *
     * @return Ticket
     */
    public function setTicketType(\App\Entity\TicketType $ticketType = null)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get ticketType
     *
     * @return \App\Entity\TicketType
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Set box
     *
     * @param \App\Entity\Box $box
     *
     * @return Ticket
     */
    public function setBox(\App\Entity\Box $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return \App\Entity\Box
     */
    public function getBox()
    {
        return $this->box;
    }
}

