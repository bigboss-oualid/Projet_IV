<?php

namespace App\Controller;


use App\Form\OrderType;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BookingController extends AbstractController
{
	/**
	 * @Route("/booking", name="booking")
	 * @param Request     $request
	 *
	 * @param CartService $cartService
	 *
	 * @return Response
	 */
    public function booking(Request $request, CartService $cartService): Response
    {
	    $form = $this->createForm(OrderType::class);
	    $form->remove('visitors');
	    $form->handleRequest($request);

	    if($form->isSubmitted() && $form->isValid()) {
	    	$cartService->addOrder($form->getData());
		    $this->addFlash('success', 'Veuillez entrer les détails pour vos billets');

			return $this->redirectToRoute('visitors');
	    }
        return $this->render('pages/booking/order.html.twig', [
	        'current_menu'  => 'Booking',
	        'form'          => $form->createView(),
	        'cart'          => $cartService->fullCart()
        ]);
    }

	/**
	 * @Route("booking/visitors", name="visitors")
	 * @param Request     $request
	 *
	 * @param CartService $cartService
	 *
	 * @return Response
	 */
	public function visitors(Request $request, CartService $cartService): Response
    {
	    $form = $this->createForm(OrderType::class);
	    $form->remove('visitorsNbr')
	         ->remove('fullDay')
	         ->remove('reservedFor');

	    $form->handleRequest($request);

	    if($form->isSubmitted() && $form->isValid()) {

		   $cartService->saveTickets($form->getData());

		    return $this->redirectToRoute('cart');
	    }

	    return $this->render('pages/booking/visitors.html.twig', [
		    'current_menu'  => 'Booking',
		    'form'          => $form->createView(),
		    'visitorsNbr'   => $cartService->getLastOrder()->getVisitorsNbr(),
		    'cart'          => $cartService->fullCart()
	    ]);
    }

	/**
	 * @Route("booking/cart", name="cart")
	 *
	 * @param CartService $cartService
	 *
	 * @return Response
	 */
    public function cart(CartService $cartService): Response
    {
	    $cartInfo = $cartService->getCartInfo();

	    return $this->render('pages/booking/cart.html.twig', [
		    'current_menu'  => 'Booking',
		    'orders'        => $cartInfo['cart'],
		    'last_price'    => $cartInfo['last_price'],
		    'total_visitor_nbr'  => $cartInfo['total_visitor_nbr'],
		    'cart'          => $cartService->fullCart()
	    ]);
    }

	/**
	 * @Route("booking/remove/{idOrder}/{idVisitor}",
	 *      name="remove")
	 * @param int         $idOrder
	 * @param int         $idVisitor
	 *
	 * @param CartService $cartService
	 *
	 * @return RedirectResponse
	 */
    public function remove(int $idOrder, int $idVisitor=null, CartService $cartService): RedirectResponse
    {
		if($idVisitor == null){
			$cartService->deleteOrder($idOrder);
			$this->addFlash('success', 'Le bouquet des billets n° ' .$idOrder. 'est supprimé avec succès');
		}
        else{
			$cartService->deleteTicket($idOrder, $idVisitor);
		    $this->addFlash('success', 'Le billet n° '. $idVisitor .' du bouquet '. $idOrder .' est supprimé avec succès');
        }

	    return $this->redirectToRoute('cart');
    }

	/**
	 * @Route("booking/cart/cc",
	 *      name="cleancart")
	 * @param CartService $cartService
	 *
	 * @return RedirectResponse
	 */
	public function CleanCart(CartService $cartService): RedirectResponse
	{
		$cartService->clean();
		return $this->redirectToRoute('cart');
	}




	/*
	/**
	 * * filter Ticket & then add it EX: end($cart)->getVisitors()
	 *
	 * @param Collection $NewVisitors
	 * @param Collection $orderedTicket
	 */
	/*
    private function addMoreTickets(Collection $NewVisitors, Collection $orderedTicket){

	    foreach($NewVisitors as $visitor){
    		$orderedTicket->add($visitor);
	    }
    }
	*/
}
