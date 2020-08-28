<?php

namespace CarMarketBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use CarMarketBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
	/**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("contact", name="create_contact", method={"GET"})
     */
    public function create()
    {
        $contact = $this->getDoctrine()->getRepository(Contact::class)->findBy(['user' => $this->getUser()]);
        return $this->render('users/contact/create.html.twig', ['contact' => $contact, 'form' => $this->createForm(ContactType::class)->createView()]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("contact", method={"POST"})
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

        var_dump('tuk');
        exit;
    }
}