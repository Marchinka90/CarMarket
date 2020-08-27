<?php

namespace CarMarketBundle\Controller;

use CarMarketBundle\Entity\Role;
use CarMarketBundle\Entity\User;
use CarMarketBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @Route("register", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerProcess(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $email = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $form['email']->getData()]);

        if ($email !== null) {
            $email = $email->getEmail();
            $this->addFlash("errors", "Email $email already taken");
            return $this->returnRegisterView($user);
        }

        if($form['password']['first']->getData() !== $form['password']['second']->getData()){
            $this->addFlash("errors", "Password mismatch");
            return $this->returnRegisterView($user);
        }

        $passwordHash = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
        $user->setPassword($passwordHash);

        $isAdmin = $this->getDoctrine()->getRepository(User::class)->findAll();

        if (!$isAdmin) {
            $role = $this
                ->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['name' => 'ROLE_ADMIN']);
        } else {
             $role = $this
                ->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['name' => 'ROLE_ADMIN']);
        }

        var_dump($role);
        exit;
        
        $user->addRole($role);
        $user->setStatus(1);
        
        $em = $this->getDoctrine()->getManager();
		$em->persist($user);
		$em->flush();

		$this->addFlash("success", "User was created successfuly");
        return $this->redirectToRoute('user_register');
        
    }

    /**
     * @param User $user
     * @return Response
     */
    private function returnRegisterView(User $user): Response
    {
        return $this->render('users/register.html.twig', ['user' => $user, 'form' => $this->createForm(UserType::class)->createView()]);

    }
}