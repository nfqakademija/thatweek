<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ParticipantType;
use AppBundle\Service\Calendar;
use AppBundle\Entity\Participant;
use AppBundle\Service\ParticipantFormHandler;
use AppBundle\Service\UserHandler;
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
                               ParticipantFormHandler $formHandler,
                                UserHandler $userHandler)
    {
        $userId = $this->getUser()->getId();
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);

        if($formHandler->handle($request, $form, $userId))
            return $this->redirectToRoute('order.showInfo');//reseting form

        $weeks = $calendar->getWeeks();
        $participants = $userHandler->getParticipants($userId);

        return $this->render('AppBundle:Home:product.html.twig', array(
            'weeks' => $weeks,
            'participants' => $participants,
            'participantForm' => $form->createView()
        ));
    }

}
