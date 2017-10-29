<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/*
 * Class ProductController
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/product")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Product:index.html.twig', array(
            // ...
        ));
    }

}
