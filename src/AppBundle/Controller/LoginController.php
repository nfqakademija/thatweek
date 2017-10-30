<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\OAuthClient;

/**
 * Class LoginController
 * @Route("/login")
 *
 */
class LoginController extends Controller
{

    /**
     * @Route("/facebook", name="login.facebook")
     * @param OAuthClient $auth
     */
    public function facebookLoginAction(Request $request)
    {
        $client = $this->container->get('AppBundle\Utils\OAuthClient');
        $client->connect($request);
        return $this->render('AppBundle:Home:index.html.twig', [
        "info" => 'd'
    ]);
    }

    /**
     * @Route("/facebook/check", name="login.facebook.check")
     * @param OAuthClient $client
     */
    public function facebookLoginCheckAction(OAuthClient $client)
    {
        return $this->redirectToRoute('homepage');
    }


}
