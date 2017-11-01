<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class HomeController
 * @Route("/")
 *
 */
class HomeController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        return $this->render('AppBundle:Home:index.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }

}
