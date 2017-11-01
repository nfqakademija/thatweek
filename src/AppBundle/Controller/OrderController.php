<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\Calendar;

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
      //  exit(\GuzzleHttp\json_encode($weeks[0]->getStartDate()));
        return $this->render('AppBundle:Home:product.html.twig', array(
            'weeks' => $weeks
        ));
    }

}
