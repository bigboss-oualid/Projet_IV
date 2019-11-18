<?php

namespace App\Notification;

use App\Entity\Contact;
use Swift_Attachment;
use Twig\Environment;

class EmailNotification
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;
	/**
	 * @var Environment
	 */
	private $renderer;
	private $kernelProjectDir;

	public function __construct(\Swift_Mailer $mailer, Environment $renderer, $kernelProjectDir)
	{
		$this->mailer = $mailer;
		$this->renderer = $renderer;
		$this->kernelProjectDir = $kernelProjectDir;
	}

	/**
	 * @param Contact $contact
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function notifyContact(Contact $contact)
	{
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

	/**
	 * @param int    $paymentCardId
	 * @param string $receiptEmail
	 *
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function notifyClient(int $paymentCardId, string $receiptEmail)
	{
		$message = (new \Swift_Message('Sujet : Billets + facture d\'achat'))
			->setFrom('email@louvre.de')
			->setTo($receiptEmail)
			->setReplyTo('email@louvre.fr')
			->setBody(
				$this->renderer->render('emails/paymentSuccess.html.twig'), 'text/html')
			->attach(Swift_Attachment::fromPath($this->kernelProjectDir.'/public/pdf/tickets'.$paymentCardId.'.pdf'))
			->attach(Swift_Attachment::fromPath($this->kernelProjectDir.'/public/pdf/invoice'.$paymentCardId.'.pdf'));
		$this->mailer->send($message);
	}

}