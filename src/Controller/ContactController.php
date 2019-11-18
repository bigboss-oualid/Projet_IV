<?php

namespace App\Controller;


use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\EmailNotification;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
	/**
	 * @Route("/contact", name="contact")
	 *
	 * @param Request           $request
	 * @param EmailNotification $notification
	 * @param CartService       $cartService
	 *
	 * @return Response
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function contact(Request $request, EmailNotification $notification, CartService $cartService): Response
	{
		$contact = new Contact();
		$form = $this->createForm(ContactType::class, $contact);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()){
			$notification->notifyContact($contact);
			$this->addFlash('success', 'Votre email a bien été envoyé');
			return $this->redirectToRoute('contact');

		}
		$cartInfo = $cartService->getCartInfo();
		return $this->render('pages/contact.html.twig', [
			'current_menu' => 'Contact',
			'form' => $form->createView(),
			'cart'          => $cartInfo,
		]);
	}
}