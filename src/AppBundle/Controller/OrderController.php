<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Form\OrderType;
use AppBundle\Service\OrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ParticipantType;
use AppBundle\Service\Calendar;
use AppBundle\Entity\Participant;
use AppBundle\Service\ParticipantFormHandler;
use AppBundle\Service\UserHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class OrderController
 * @Route("/order")
 */
class OrderController extends Controller
{
    /**
     * @Route("/show", name="order.showInfo")
     */
    public function ShowAction(Request $request,
                                UserHandler $userHandler,
                                ParticipantFormHandler $participantFormHandler,
                                OrderHandler $orderHandler)
    {

        $user = $this->getUser()->getEntity();
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class, $participant);

        if($participantFormHandler->handle($request, $participantForm, $user))
        {
            $participants = $userHandler->participantsToArray($user->getParticipants());
            return new JsonResponse($participants);
        }
        $participants = $userHandler->participantsToArray($user->getParticipants());

        $order = new Order();
        $orderForm = $this->createForm(OrderType::class, $order);
        if($orderHandler->handle($request, $orderForm, $user, $order)) {
            return $this->redirectToRoute('profile.show.orders');
        }

        return $this->render('AppBundle:Home:product.html.twig', array(
            'participants' => $participants,
            'participantForm' => $participantForm->createView(),
            'orderForm' => $orderForm->createView(),
            'serverTime' => strtotime(date('Y-m-d'))
        ));
    }

    /**
     * @Route("/check", name="order.check")
     */
    public function checkAction(Request $request, OrderHandler $orderHandler)
    {
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $days = $orderHandler->getDaysWithOrders($startDate, $endDate);
        return new JsonResponse($days);
    }

    /**
     * @Route("/get", name="order.get")
     */
    public function getAction(Request $request, OrderHandler $orderHandler)
    {
        $date = $request->request->get('date');
        $orders = $orderHandler->getOrdersInDay($date);
        return new JsonResponse($orders);
    }
}
