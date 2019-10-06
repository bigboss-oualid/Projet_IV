<?php

namespace App\Notification;

use App\Entity\Contact;
use Twig\Environment;

class ContactNotification
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;
	/**
	 * @var Environment
	 */
	private $renderer;

	public function __construct(\Swift_Mailer $mailer, Environment $renderer)
	{
		$this->mailer = $mailer;
		$this->renderer = $renderer;
	}

	/**
	 * @param Contact $contact
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function notify(Contact $contact) {
		$message = (new \Swift_Message('Sujet : ' . $contact->getSubject()))
			->setFrom('noreply@louvre.de')
			->setTo('email@louvre.fr')
			->setReplyTo($contact->getEmail())
			->setBody(
				$this->renderer->render('emails/contact.html.twig', [
					'contact' => $contact
				]), 'text/html');
		$this->mailer->send($message);
	}


}