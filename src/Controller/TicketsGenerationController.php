<?php
namespace App\Controller;

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
	 * @param Request     $request
	 *
	 * @param Pdf         $ticketsAsPdf $ticketsAsPdf
	 *
	 * @param CartService $cartService
	 *
	 * @return Response
	 * @throws Error
	 */
	public function generateTickets(Request $request, Pdf $ticketsAsPdf, CartService $cartService): Response
	{
		$path = $this->getParameter('kernel.project_dir') . '/public/images/louvre-log-origo.png';
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$baseLogo = 'data:image/'.$type.';base64,'.base64_encode($data);

		$cart = $cartService->getCart();
		$html = $this->renderView('pdf/tickets.html.twig', [
			'orders' => $cart,
			'logo' => $baseLogo
		]);

		$ticketsAsPdf->generatePDF($html, 'tickets', $cart[0]->getPaymentCard()->getId());

		//show tickets in browser
		//return $html;

		// render to success page
		return $this->render('pages/booking/payment.success.html.twig', [
			'current_menu'  => 'Booking',
			'receipt_email' => $request->get('receipt_email'),
			'amount' => $request->get('amount')
		]);
	}
}