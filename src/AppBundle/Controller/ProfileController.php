<?php

namespace AppBundle\Controller;

use AppBundle\Service\UserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
/**
 * Class ProfileController
 * @package AppBundle\Controller
 * @Route("/profile")
 */
class ProfileController extends Controller
{

    /**
     * @Route("/", name="profile.show")
     */
    public function showProfileAction()
    {
        return $this->render('AppBundle:Profile:user_info.html.twig');
    }


    /**
     * @Route("/orders", name="profile.show.orders")
     */
    public function showOrdersAction(UserHandler $userHandler)
    {
        $user = $this->getUser()->getEntity();

        $orders = $userHandler->getOrders($user);
        return $this->render('AppBundle:Profile:order_history.html.twig', array(
            'orders' => $orders
        ));
    }
}
