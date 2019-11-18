<?php
namespace App\Controller;

use App\Notification\EmailNotification;
use App\Service\Cart\CartService;
use App\Service\Pdf;
use Pdfcrowd\Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketsGenerationController extends AbstractController
{
	/**
	 * @Route("/booking/payment/success/{receipt_email}/{amount}", name="success", requirements={"receipt_email"="^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$", "amount"="\d+"})
	 * @param Request           $request
	 *
	 * @param EmailNotification $notification
	 * @param Pdf               $ticketsAsPdf $ticketsAsPdf
	 *
	 * @param CartService       $cartService
	 *
	 * @return Response
	 * @throws Error
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function generateTickets(Request $request, EmailNotification $notification, Pdf $ticketsAsPdf, CartService $cartService): Response
	{
		$path = $this->getParameter('kernel.project_dir') . '/public/images/louvre-log-origo.png';
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$baseLogo = 'data:image/'.$type.';base64,'.base64_encode($data);

		$cart = $cartService->getCart();
		$paymentCardId = $cart[0]->getPaymentCard()->getId();
		$receiptEmail = $request->get('receipt_email');

		$html = $this->renderView('pdf/tickets.html.twig', [
			'orders' => $cart,
			'logo' => $baseLogo
		]);

		$ticketsAsPdf->generatePDF($html, 'tickets', $paymentCardId);

		$notification->notifyClient($paymentCardId, $receiptEmail);
		$cartService->clean();

		//show tickets in browser
		//return $html;

		// render to success page
		return $this->render('pages/booking/payment.success.html.twig', [
			'current_menu'  => 'Booking',
			'receipt_email' => $receiptEmail,
			'amount' => $request->get('amount')
		]);
	}
}