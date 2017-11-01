<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Service\Calendar;

/**
 * Class OrderController
 * @Route("/order")
 */
class OrderController extends Controller
{
    /**
     * @Route("/show", name="order.showInfo")
     */
    public function ShowAction(Calendar $calendar)
    {
        $weeks = $calendar->getWeeks();


        return $this->render('AppBundle:Home:product.html.twig', array(
            'weeks' => $weeks
        ));
    }

}
