<?php

namespace CarMarketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends Controller
{
    /**
     * @Route("/login", name="user_login")
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login()
    {
        // replace this example code with whatever you need
        return $this->render('security/login.html.twig');
    }
}