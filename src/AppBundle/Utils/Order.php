<?php

namespace AppBundle\Utils;

class Order
{
    private $participants;
    private $weekId;

    public function setParticipants($participants)
    {
        $this->participants = $participants;

        return $this;
    }

    public function getParticipants()
    {
        return $this->participants;
    }

    public function setWeekId($weekId)
    {
        $this->weekId = $weekId;

        return $this;
    }

    public function getWeekId()
    {
        return $this->weekId;
    }
}