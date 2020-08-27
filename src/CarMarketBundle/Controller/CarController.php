<?php

namespace CarMarketBundle\Controller;

use CarMarketBundle\Entity\Car;
use CarMarketBundle\Form\CarType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
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

    /**
     * @Route("/create", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request)
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        $this->uploadFile($form, $car);

        $em = $this->getDoctrine()->getManager();
		$em->persist($car);
		$em->flush();
        
        $this->addFlash('info', 'Create car successfully');
        return $this->redirectToRoute('homapage');  
    }

    /**
     * @param FormInterface $form
     * @param Car $car
     */
    private function uploadFile(FormInterface $form, Car $car)
    {
        /** @var UploadFile $file */
        $file = $form['image']->getData();
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        if ($file) {
           $file->move(
                $this->getParameter('articles_directory'),
                $fileName
           );
           $car->setImage($fileName);
        }

    }

}