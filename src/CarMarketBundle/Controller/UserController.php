<?php

namespace CarMarketBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use CarMarketBundle\Entity\Contact;
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

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $error => $value) {
                $this->addFlash("errors", $value->getMessage()); 
            }
            return $this->returnRegisterView($user);
        }

        $email = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $form['email']->getData()]);

        if ($email !== null) {
            $email = $email->getEmail();
            $this->addFlash("errors", "Email $email already taken");
            return $this->returnRegisterView($user);
        }

        if ($form['password']['first']->getData() == null) {
            $this->addFlash("errors", "Password cannot be empty");
            return $this->returnRegisterView($user);
        }

        if($form['password']['first']->getData() !== $form['password']['second']->getData()){
            $this->addFlash("errors", "Password mismatch");
            return $this->returnRegisterView($user);
        }

        $passwordHash = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
        $user->setPassword($passwordHash);

        /* First User always is admin */
        $isAdmin = $this->getDoctrine()->getRepository(User::class)->findAll();

        if (!$isAdmin[0]->isAdmin()) {
            $role = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']);

        } else {
             $role = $this->getDoctrine()->getRepository(Role::class)->findOneBy(['name' => 'ROLE_USER']);
        }
        
        $user->addRole($role);
        $user->setStatus(1);
        
        $em = $this->getDoctrine()->getManager();
		$em->persist($user);
		$em->flush();

		$this->addFlash("success", "User was created successfuly");
        return $this->redirectToRoute('user_register');
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("profile", name="user_profile")
     */
    public function profile()
    {
        $contact = $this->getDoctrine()->getRepository(Contact::class)->findBy(['user' => $this->getUser()]);
        return $this->render('users/profile.html.twig', ['user' => $this->getUser(), 'contact' => $contact]);
    }

    /**
     * @Route("/profile/edit_contact", name="user_edit_contact", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function edit()
    {   
        var_dump('tuk');
        exit;
        return $this->render('users/edit_contact.html.twig', [
            'user' => $this->userService->currentUser(),
            'form' => $this->createForm(UserType::class)->createView()]);
    }



    /**
     * @Route("/logout", name="user_logout")
     * @throws \Exeption
     */
    public function logout()
    {
        throw new \Exeption('Logout failed!');
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