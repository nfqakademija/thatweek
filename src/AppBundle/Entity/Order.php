<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Order
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderRepository")
 */
class Order
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $orderedAt;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Week", inversedBy="orders")
     * @ORM\JoinColumn(name="week_id", referencedColumnName="id")
     */
    private $week;

    /**
     * @ORM\ManyToMany(targetEntity="Participant")
     * @ORM\JoinTable(name="orders_participants",
     *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="participant_id", referencedColumnName="id")}
     *      )
     */
    private $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     *
     * @return Order
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }


    /**
     *
     * @param \DateTime $orderedAt
     *
     * @return Order
     */
    public function setOrderedAt($orderedAt)
    {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    /**
     *
     *
     *
     * @return \DateTime
     */
    public function getOrderedAt()
    {
        return $this->orderedAt;
    }

    /**
     * @return int
     */
    public function getWeek()
    {
        return $this->week;
    }

    /**
     * @param int $week
     *
     * @return Order
     */
    public function setWeek($week)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

}

