<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\OrderType;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class BookingController extends AbstractController
{


	/**
	 * @var Session
	 */
	private $session;

	public function __construct( SessionInterface $session)
	{
		$this->session = $session;
	}

	/**
	 * @Route("/booking", name="booking")
	 * @param Request           $request
	 *
	 * @param BookingRepository $repository
	 *
	 * @return Response
	 */
    public function booking(Request $request): Response
    {
    	$form = $this->createForm(OrderType::class);
	    $form->remove('visitors');
	    $form->handleRequest($request);

		/*if i need last insered ID from table Booking */
	    //$inseredID = $repository->findLatestInsertedId();

	    //dd($inseredID+1);

	    if($form->isSubmitted() && $form->isValid()) {

		    $this->session->clear();
			$session = $this->session;
		    // Create cart if doesn't exist
		    $cart = $session->get('cart', []);
		    array_push($cart ,$form->getData());
		    $session->set('cart', $cart);

		    $this->addFlash('success', 'Veuillez entrer les détails pour vos billets');
			return $this->redirectToRoute('visitors', ['sessionBid' => 0]);
	    }
        return $this->render('pages/booking/order.html.twig', [
	        'current_menu'  => 'Booking',
	        'form'          => $form->createView()
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
		    $this->addFlash('success', 'Veuillez entrer les détails pour vos billets');
			//add the new Booking values -WITH DATA'S VISITORS-
		    $cart[0] = $form->getData();
		    $session->set('cart', $cart);
		    //dd( $session->get('cart', []));

		    return $this->redirectToRoute('cart');
	    }

	    return $this->render('pages/booking/visitors.html.twig', [
		    'current_menu'  => 'Booking/visitors',
		    'form'          => $form->createView(),
		    'visitorsNbr'  => $visitorsNbr
	    ]);
    }

	/**
	 * @Route("booking/visitors/cart", name="cart")
	 *
	 * @return Response
	 */
    public function cart(): Response
    {
    	$cart = $this->session->get('cart', []);

	    return $this->render('pages/booking/cart.html.twig', [
		    'current_menu'  => 'Booking/visitors/cart',
		    'commmands'  => $cart
	    ]);
    }
}
