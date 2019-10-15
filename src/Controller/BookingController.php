<?php

namespace App\Controller;


use App\Form\OrderType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class BookingController extends AbstractController
{
	/**
	 * @var SessionInterface
	 */
	private $session;

	/**
	 * BookingController constructor.
	 *
	 * @param SessionInterface $session
	 */
	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
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
		    $session = $this->session;

		    $cart = $session->get('cart', []);
		    array_push($cart ,$form->getData());
		    $session->set('cart', $cart);

		    $this->addFlash('success', 'Veuillez entrer les détails pour vos billets');

			return $this->redirectToRoute('visitors');
	    }
        return $this->render('pages/booking/order.html.twig', [
	        'current_menu'  => 'Booking',
	        'form'          => $form->createView(),
	        'cart'          => $this->fullCart()
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
	    $session = $this->session;
	    $cart = $session->get('cart',[]);

	    //Get Number of visitors
	    $visitorsNbr = end($cart)->getVisitorsNbr();

	    $form = $this->createForm(OrderType::class);
	    $form->remove('visitorsNbr')
	         ->remove('fullDay')
	         ->remove('reservedFor');
	    $form->handleRequest($request);

	    if($form->isSubmitted() && $form->isValid()) {
			//copy visitors Values in session

		    $this->saveTickets($form, $cart);
		    $session->set('cart', $cart);

		    return $this->redirectToRoute('cart');
	    }

	    return $this->render('pages/booking/visitors.html.twig', [
		    'current_menu'  => 'Booking',
		    'form'          => $form->createView(),
		    'visitorsNbr'   => $visitorsNbr,
		    'cart'          => $this->fullCart()
	    ]);
    }

	/**
	 * @Route("booking/cart", name="cart")
	 *
	 * @return Response
	 */
    public function cart(): Response
    {
    	$cart = $this->session->get('cart', []);
		dump($cart);
		$lastPrice = 0;
	    $this->removeEmptyOrder($cart);
    	foreach($cart as $key => $booking){
		    $booking->setTotalPrice();
		    $lastPrice += $booking->getTotalPrice();
	    }

	    $allVisitors = $this->countTickets($cart);
	    $this->session->set('cart', $cart);

	    return $this->render('pages/booking/cart.html.twig', [
		    'current_menu'  => 'Booking',
		    'orders'        => $cart,
		    'last_price'    => $lastPrice,
		    'all_visitors'  => $allVisitors,
		    'cart'          => $this->fullCart()
	    ]);
    }

	/**
	 * @Route("booking/remove/{idBooking}/{idVisitor}",
	 *      name="remove")
	 * @param int $idBooking
	 * @param int $idVisitor
	 *
	 * @return RedirectResponse
	 */
    public function remove(int $idBooking, int $idVisitor=null): RedirectResponse
    {
	    $cart = $this->session->get('cart', []);
	    $order = &$cart[$idBooking - 1];
	    if($idVisitor == null){
		    unset($cart[$idBooking-1]);
		    $cart = array_values($cart);
		    $this->addFlash('success', 'Le bouquet des billets n° ' .$idBooking. 'est supprimé avec succès');
	    }
	    else{
			if(!empty($order)){
			    $order->getVisitors()->remove($idVisitor - 1);
			    if (!$order->getVisitors()->isEmpty()) {
				    $items = new ArrayCollection();
				    $countVisitors = 0;
				    foreach ($order->getVisitors() as $item) {
					    $items->add($item);
					    $countVisitors++;
				    }

				    $order->addVisitors($items);
				    $order->setVisitorsNbr($countVisitors);
			    }else{
				    unset($cart[$idBooking-1]);
				    $cart = array_values($cart);
			    }
		    }
		    $this->addFlash('success', 'Le billet n° '. $idVisitor .' est supprimé avec succès');
	    }

	    $this->session->set('cart', $cart);


	    return $this->redirectToRoute('cart');
    }

	/**
	 * @Route("booking/cart/cc",
	 *      name="cleancart")
	 * @return RedirectResponse
	 */
	public function CleanCart(): RedirectResponse
	{
		$this->session->remove('cart');

		return $this->redirectToRoute('cart');
	}

	private function fullCart() {
    	if(empty($this->session->get('cart'))){
    		return null;
	    }
	    return 'full';
    }

	/**
	 * *Delete from cart empty order
	 *
	 * @param array $cart
	 */
    private function removeEmptyOrder(array &$cart){
	    foreach($cart as $key => $booking){
		    if($booking->getVisitors()->isEmpty()){
			    unset($cart[$key]);
		    }
	    }
	    $cart = array_values($cart);
	    $this->session->set('cart', $cart);
    }


	/**
	 * @param FormInterface $form
	 * @param array         $cart
	 */
	private function saveTickets(FormInterface $form, array  $cart){
        end($cart)->addVisitors($form->getData()->getVisitors());
	}

	private function countTickets(array &$cart) {
		$allVisitors = 0;
		foreach($cart as $booking){
			if($visitors = $booking->getVisitors()){
				$allVisitors += count($visitors);
			}
		}
		return $allVisitors;
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
