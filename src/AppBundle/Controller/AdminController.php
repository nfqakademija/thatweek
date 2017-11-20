<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Form\DayUpdateType;
use AppBundle\Form\OrderType;
use AppBundle\Service\DayHandler;
use AppBundle\Service\OrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package AppBundle\Controller
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin.home")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin:index.html.twig');
    }

    /**
     * @Route("/calendar", name="admin.calendar")
     */
    public function showDaysAction(Request $request, DayHandler $dayHandler)
    {
        $daysUpdateForm = $this->createForm(DayUpdateType::class);

        $dayHandler->handle($request, $daysUpdateForm);

        return $this->render('AppBundle:Admin:day_info.html.twig', array(
            'update_form' => $daysUpdateForm->createView()
        ));
    }

    /**
     * @Route("/order/edit/{id}", name="admin.order.edit")
     */
    public function editAction(Request $request, OrderHandler $orderHandler, DayHandler $dayHandler, $id)
    {
        $order = $orderHandler->getOrder($id);
        $dates = array(
            'startDate' => $order->getStartDate()->getTimestamp(),
            'endDate' => $order->getEndDate()->getTimestamp()
        );
        $user = $order->getUser();
        $orderEditForm = $this->createForm(OrderType::class, $order);


        if($orderHandler->handle($request, $orderEditForm, $user, $order))
            return $this->redirectToRoute('admin.calendar');

        $daysUpdateForm = $this->createForm(DayUpdateType::class);
        $participants = $orderHandler->getParticipants($order, $user);

        $dayHandler->handle($request, $daysUpdateForm);

        return $this->render('AppBundle:Admin:order_edit.html.twig', array(
            'participants' => $participants,
            'dates'=> $dates,
            'orderForm' => $orderEditForm->createView(),
            'update_form' => $daysUpdateForm->createView()
        ));
    }
}
