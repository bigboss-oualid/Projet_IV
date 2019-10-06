<?php

namespace App\Controller;


use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
	/**
	 * @Route("/contact", name="contact")
	 * @param Request             $request
	 * @param ContactNotification $notification
	 *
	 * @return Response
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function index(Request $request, ContactNotification $notification): Response
	{
		$contact = new Contact();
		$form = $this->createForm(ContactType::class, $contact);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()){
			$notification->notify($contact);
			$this->addFlash('success', 'Votre email a bien été envoyé');
			return $this->redirectToRoute('contact');

		}
		return $this->render('pages/contact.html.twig', [
			'current_menu' => 'Contact',
			'form' => $form->createView()
		]);
	}
}