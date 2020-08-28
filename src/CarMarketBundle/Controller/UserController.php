<?php

namespace CarMarketBundle\Controller;

use CarMarketBundle\Entity\Contact;
use CarMarketBundle\Entity\Role;
use CarMarketBundle\Entity\User;
use CarMarketBundle\Form\ContactType;
use CarMarketBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("change_password", name="change_password", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function passwordChange()
    {
        return $this->render('users/password.html.twig', ['user' => $this->getUser(), 'form' => $this->createForm(UserType::class)->createView()]);
    }

    /**
     * @Route("change_password", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function passwordChangeProcess(Request $request)
    {
        $user = $this->getUser();
        $currentPassword = $request->request->get('user');
        $currentPassword = $currentPassword['old_password'];

        if ($currentPassword == null) {
            $this->addFlash("errors", "Current Password cannot be empty");
            return $this->render('users/password.html.twig', ['user' => $this->getUser(), 'form' => $this->createForm(UserType::class)->createView()]);
        }

        $valid = $this->get('security.password_encoder')->isPasswordValid($user, $currentPassword);
        if (!$valid) {
            $this->addFlash("errors", "Wrong Current Password");
            return $this->render('users/password.html.twig', ['user' => $this->getUser(), 'form' => $this->createForm(UserType::class)->createView()]);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->remove('username');
        $form->remove('email');
        $form->remove('status');
        $form->handleRequest($request);
        
        if ($form['password']['first']->getData() == null) {
            $this->addFlash("errors", "New Password cannot be empty");
            return $this->render('users/password.html.twig', ['user' => $this->getUser(), 'form' => $this->createForm(UserType::class)->createView()]);
        }

        if($form['password']['first']->getData() !== $form['password']['second']->getData()){
            $this->addFlash("errors", "New Password mismatch");
            return $this->render('users/password.html.twig', ['user' => $this->getUser(), 'form' => $this->createForm(UserType::class)->createView()]);
        }

        $passwordHash = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
        $user->setPassword($passwordHash);

        $em = $this->getDoctrine()->getManager();
        $em->merge($user);
        $em->flush();

        $this->addFlash("success", "Password changed successfuly");
        return $this->redirectToRoute('user_profile');
    }

    /**
     * @Route("profile", name="user_profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function profile()
    {
        $contact = $this->getDoctrine()->getRepository(Contact::class)->find($this->getUser()->getId());
        return $this->render('users/profile.html.twig', ['user' => $this->getUser(), 'contact' => $contact]);
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