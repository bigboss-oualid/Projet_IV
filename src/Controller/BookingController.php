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
	 * @var CartService
	 */
	private $cartService;

	/**
	 * BookingController constructor.
	 *
	 * @param CartService $cartService
	 */
	public function __construct(CartService $cartService)
	{
		$this->cartService = $cartService;
	}

	/**
	 * @Route("/booking", name="booking")
	 * @param Request $request
	 *
	 * @return Response
	 */
    public function booking(Request $request): Response
    {
	    $form = $this->createForm(OrderType::class);
	    $form->remove('visitors');
	    $form->handleRequest($request);
	    if($form->isSubmitted() && $form->isValid()) {
		    $this->cartService->addOrder($form->getData());
		    $this->addFlash('success', 'Veuillez entrer les détails pour vos billets');

			return $this->redirectToRoute('visitors');
	    }
        return $this->render('pages/booking/order.html.twig', [
	        'current_menu'  => 'Booking',
	        'form'          => $form->createView(),
	        'cart'          => $this->cartService->fullCart()
        ]);
    }

	/**
	 * @Route("booking/visitors", name="visitors")
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function visitors(Request $request): Response
    {
	    $form = $this->createForm(OrderType::class);
	    $form->remove('visitorsNbr')
	         ->remove('fullDay')
	         ->remove('reservedFor');
	    $form->setData($this->cartService->getLastOrder());

	    $form->handleRequest($request);

	    //dd($form->getData()->getReservedFor());
	    if($form->isSubmitted() && $form->isValid()) {
		   $this->cartService->saveTickets($form->getData());

		    return $this->redirectToRoute('cart');
	    }

	    return $this->render('pages/booking/visitors.html.twig', [
		    'current_menu'  => 'Booking',
		    'form'          => $form->createView(),
		    'visitorsNbr'   => $this->cartService->getLastOrder()->getVisitorsNbr(),
		    'cart'          => $this->cartService->fullCart()
	    ]);
    }

	/**
	 * @Route("booking/cart", name="cart")
	 *
	 * @return Response
	 */
    public function cart(): Response
    {
	    $cartInfo = $this->cartService->getCartInfo();

	    return $this->render('pages/booking/cart.html.twig', [
		    'current_menu'  => 'Booking',
		    'orders'        => $cartInfo['cart'],
		    'last_price'    => $cartInfo['last_price'],
		    'total_visitor_nbr'  => $cartInfo['total_visitor_nbr'],
		    'cart'          => $this->cartService->fullCart()
	    ]);
    }

	/**
	 * @Route("booking/remove/{idOrder}/{idVisitor}",
	 *      name="remove")
	 * @param int $idOrder
	 * @param int $idVisitor
	 *
	 * @return RedirectResponse
	 */
    public function remove(int $idOrder, int $idVisitor=null): RedirectResponse
    {
		if($idVisitor == null){
			$this->cartService->deleteOrder($idOrder);
			$this->addFlash('success', 'Le bouquet des billets n° ' .$idOrder. ' est supprimé avec succès');
		}
        else{
			$this->cartService->deleteTicket($idOrder, $idVisitor);
		    $this->addFlash('success', 'Le billet n° '. $idVisitor .' du bouquet '. $idOrder .' est supprimé avec succès');
        }

	    return $this->redirectToRoute('cart');
    }

	/**
	 * @Route("booking/cart/cc",
	 *      name="cleancart")
	 * @return RedirectResponse
	 */
	public function CleanCart(): RedirectResponse
	{
		$this->cartService->clean();
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
