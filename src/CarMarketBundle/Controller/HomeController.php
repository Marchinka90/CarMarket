<?php

namespace CarMarketBundle\Controller;

use CarMarketBundle\Entity\Car;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        // $cars = $this->getDoctrine()->getRepository(Car::class)->findBy([], ['dateAdded' => 'DESC']);

        $repository = $this->getDoctrine()->getRepository(Car::class);
        $query = $repository->createQueryBuilder('c')
            ->orderBy('c.dateAdded', 'DESC')
            ->getQuery();
        $cars = $query->setMaxResults(4)->getResult();

        return $this->render('home/index.html.twig', [
            // 'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'cars' => $cars
        ]);
    }
}
