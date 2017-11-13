<?php

namespace AppBundle\Controller;

use AppBundle\Form\DayUpdateType;
use AppBundle\Service\DayHandler;
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
    public function indexAction(Request $request, DayHandler $dayHandler)
    {
        $updateForm = $this->createForm(DayUpdateType::class);

        $dayHandler->handle($request, $updateForm);

        return $this->render('AppBundle:Admin:calendar_manager.html.twig', array(
            'serverTime' => strtotime(date('Y-m-d')),
            'update_form' => $updateForm->createView()
        ));
    }
}
