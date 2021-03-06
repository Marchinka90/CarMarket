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
        
        $validator = $this->get('validator');
        $errors = $validator->validate($car);
        if (count($errors) > 0) {
            foreach ($errors as $error => $value) {
                $this->addFlash("errors", $value->getMessage()); 
            }
            return $this->render('cars/create.html.twig', ['car' => $car, 'form' => $this->createForm(CarType::class)->createView()]);
        }


        $this->uploadFile($form, $car);
        $car->setAuthor($this->getUser());
        $em = $this->getDoctrine()->getManager();
		$em->persist($car);
		$em->flush();
        
        $this->addFlash('success', 'Car created successfully');
        return $this->redirectToRoute('my_cars');  
    }

    /**
     * @Route("car/{id}", name="car_view")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view($id)
    {
        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);

        if ($car == null) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('cars/view.html.twig', ['car' => $car]);
    }

    /**
     * @Route("car/{id}/delete", name="car_delete", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(int $id)
    {
        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);

        if ($car == null) {
            return $this->redirectToRoute('my_cars');
        }

        if (!$this->isAuthorOrAdmin($car)) {
            return $this->redirectToRoute('homepage'); 
        }

        return $this->render('cars/delete.html.twig', ['form' => $this->createForm(CarType::class)->createView(),
            'car' => $car
        ]);
    }

    /**
     * @Route("car/{id}/delete_confirmed", name="car_delete_confirmed", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $em->remove($car);
        $em->flush();
        $this->addFlash('success', 'Car deleted successfully');
        return $this->redirectToRoute('my_cars');
    }

    /**
     * @Route("car/{id}/edit", name="car_edit", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(int $id)
    {
        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);

        if ($car == null) {
            return $this->redirectToRoute('my_cars');
        }

        if (!$this->isAuthorOrAdmin($car)) {
            return $this->redirectToRoute('homepage'); 
        }

        return $this->render('cars/edit.html.twig', ['form' => $this->createForm(CarType::class) ->createView(),
            'car' => $car
        ]);
    }

    /**
     * @Route("car/{id}/edit", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editProcess(Request $request, int $id)
    {
        $car = $this->getDoctrine()->getRepository(Car::class)->find($id);
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        $validator = $this->get('validator');
        $errors = $validator->validate($car);

        if (count($errors) > 0) {
            foreach ($errors as $error => $value) {
                $this->addFlash("errors", $value->getMessage()); 
            }
            return $this->render('cars/edit.html.twig', ['car' => $car, 'form' => $this->createForm(CarType::class)->createView()]);
        }

        $this->uploadFile($form, $car);
        
        $em = $this->getDoctrine()->getManager();
        $em->merge($car);
        $em->flush();

        $this->addFlash('success', 'Car edited successfully');
        return $this->redirectToRoute('my_cars');
    }

    /**
     * @Route("/cars/my_cars", name="my_cars")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllCarsByUser()
    {
        $cars = $this->getDoctrine()->getRepository(Car::class)->findBy(['authorId' => $this->getUser()]);

        return $this->render('cars/my_cars.html.twig', ['cars' => $cars]);
    }

    /**
     * @Route("/cars", name="all_cars", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllCars()
    {
        $cars = $this->getDoctrine()->getRepository(Car::class)->findBy([]);

        return $this->render('cars/all_cars.html.twig', ['cars' => $cars]);
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
                $this->getParameter('car_directory'),
                $fileName
           );
           $car->setImage($fileName);
        }
    }

    /**
     * @param Car $car
     * @return bool
     */
    private function isAuthorOrAdmin(Car $car)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($car) && !$currentUser->isAdmin()) {
            return false;
        }

        return true;
    }



}