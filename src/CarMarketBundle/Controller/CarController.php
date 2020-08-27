<?php

namespace CarMarketBundle\Controller;

use CarMarketBundle\Form\CarType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CarController extends Controller
{
	/**
     * @Route("/create", name="car_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        return $this->render('cars/create.html.twig', ['form' => $this->createForm(CarType::class)->createView()]);
    } 
}