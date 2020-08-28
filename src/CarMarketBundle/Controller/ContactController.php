<?php

namespace CarMarketBundle\Controller;

use CarMarketBundle\Entity\Contact;
use CarMarketBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
	/**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("contact", name="create_contact", methods={"GET"})
     */
    public function create()
    {
        $contact = $this->getDoctrine()->getRepository(Contact::class)->findBy(['user' => $this->getUser()]);
        return $this->render('users/contact/create.html.twig', ['contact' => $contact, 'form' => $this->createForm(ContactType::class)->createView()]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("contact", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProcess(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        $validator = $this->get('validator');
        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            foreach ($errors as $error => $value) {
                $this->addFlash("errors", $value->getMessage()); 
            }
            return $this->render('users/contact/create.html.twig', ['contact' => $contact, 'form' => $this->createForm(ContactType::class)->createView()]);
        }

        $contact->setUser($this->getUser());

        $em = $this->getDoctrine()->getManager();
		$em->persist($contact);
		$em->flush();

		$this->addFlash("success", "Contact was created successfuly");
        return $this->redirectToRoute('user_profile');
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("contact/edit", name="edit_contact", methods={"GET"})
     */
    public function edit()
    {
        $contact = $this->getDoctrine()->getRepository(Contact::class)->findBy(['user' => $this->getUser()]);
        return $this->render('users/contact/create.html.twig', ['contact' => $contact, 'form' => $this->createForm(ContactType::class)->createView()]);
    }
}