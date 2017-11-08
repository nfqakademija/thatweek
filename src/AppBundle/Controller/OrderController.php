<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Form\OrderType;
use AppBundle\Service\OrderFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ParticipantType;
use AppBundle\Service\Calendar;
use AppBundle\Entity\Participant;
use AppBundle\Service\ParticipantFormHandler;
use AppBundle\Service\UserHandler;
use Symfony\Component\HttpFoundation\Response;

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
                               Calendar $calendar,
                                UserHandler $userHandler,
                                OrderFormHandler $orderFormHandler)
    {

        $user = $this->getUser()->getEntity();
        $participant = new Participant();
        $ParticipantForm = $this->createForm(ParticipantType::class, $participant);

        $participants = $userHandler->hydrateParticipants($user->getParticipants());

        $order = new Order();
        $orderForm = $this->createForm(OrderType::class, $order);
        if($orderFormHandler->handle($request, $orderForm, $user, $order))
            return $this->redirectToRoute('profile.show.orders');

        return $this->render('AppBundle:Home:product.html.twig', array(
            'participants' => $participants,
            'participantForm' => $ParticipantForm->createView(),
            'orderForm' => $orderForm->createView()
        ));
    }

    /**
     * @Route("/proceed", name="order.proceed")
     */
    public function proceedAction(Request $request)
    {

        $request->request->get('data');
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/participant/add", name="order.participant.add")
     */
    public function addParticipantAction(Request $request,  ParticipantFormHandler $formHandler)
    {
        $user = $this->getUser()->getEntity();
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class, $participant);

        $participant = $formHandler->handle($request, $participantForm, $user);
            return new Response(json_encode($participant));
    }

}
