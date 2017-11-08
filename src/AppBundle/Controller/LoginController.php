<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Service\OAuthClient;

/**
 * Class LoginController
 * @Route("/login")
 *
 */
class LoginController extends Controller
{

    /**
     * @Route("/facebook", name="login.facebook")
     */
    public function facebookLoginAction(Request $request, OAuthClient $client)
    {
        $client->connect($request);
        return $this->render('AppBundle:Home:index.html.twig', [
        "info" => 'd'
    ]);
    }

    /**
     * @Route("/facebook/check", name="login.facebook.check")
     */
    public function facebookLoginCheckAction(OAuthClient $client)
    {
        return $this->redirectToRoute('profile.show');
    }


}
