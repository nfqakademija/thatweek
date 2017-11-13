<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Day
 *
 * @ORM\Table(name="days")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DayRepository")
 */
class Day
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
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \int
     *
     * @ORM\Column(name="capacity", type="integer")
     */
    private $capacity;

    /**
    * @ORM\ManyToMany(targetEntity="Order")
    * @ORM\JoinTable(name="days_orders",
    *      joinColumns={@ORM\JoinColumn(name="day_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")}
    *      )
    */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     * @return Day
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @param $order
     * @return $this Day
     */
    public function addOrder($order)
    {
        $this->orders->add($order);
        return $this;
    }
}

