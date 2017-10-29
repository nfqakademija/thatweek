<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Utils\OAuthClient;

/*
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
       // $em = $this->getDoctrine()->getManager();
       // $productRepo = $em->getRepository(Product::class);
       // $product = $productRepo
        $name = "not set";
        if(isset($_SESSION['Name']))
        {
            $name = $_SESSION['Name'];
        }
        return $this->render('AppBundle:Home:index.html.twig', [
            "info" => $name
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $auth =  new  OAuthClient();
        $auth->connect();

    }
}
