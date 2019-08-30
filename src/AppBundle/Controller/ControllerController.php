<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ControllerController extends Controller
{
    /**
     * @Route("/new")
     */
    public function newAction()
    {
        return $this->render('AppBundle:Controller:new.html.twig', array(
            // ...
        ));
    }

}
