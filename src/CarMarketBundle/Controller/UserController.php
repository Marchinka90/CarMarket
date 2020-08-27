<?php

namespace CarMarketBundle\Controller;

use CarMarketBundle\Entity\Car;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("register", name="user_register", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {   
        return $this->render('users/register.html.twig', ['form' => $this->createForm(UserType::class)->createView()]);
    }
}