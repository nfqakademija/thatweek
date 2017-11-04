<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Week
 *
 * @ORM\Table(name="weeks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WeekRepository")
 */
class Week
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
     * @ORM\Column(name="start_date", type="integer")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="integer")
     */
    private $endDate;

    /**
     * @var int
     *
     * @ORM\Column(name="unitsSold", type="integer")
     */
    private $unitsSold;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="week")
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
     * Set startDate
     *
     * @param \int $startDate
     *
     * @return Week
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \int
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \int $endDate
     *
     * @return Week
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return int
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set unitsSold
     *
     * @param integer $unitsSold
     *
     * @return Week
     */
    public function setUnitsSold($unitsSold)
    {
        $this->unitsSold = $unitsSold;

        return $this;
    }

    /**
     * Get unitsSold
     *
     * @return int
     */
    public function getUnitsSold()
    {
        return $this->unitsSold;
    }
}

