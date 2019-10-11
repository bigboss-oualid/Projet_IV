<?php

namespace App\Controller;


use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BookingController extends AbstractController
{
	/**
	 * @Route("/booking", name="booking")
	 * @param Request $request
	 *
	 * @return Response
	 */
    public function booking(Request $request): Response
    {
	    /*dummy code
	    $booking2= new Booking();
	    $visitor = new Visitor();
	    $visitor->setFirstName('BigBoss');
	    $booking2->getVisitors()->add($visitor);
	    */

    	$form = $this->createForm(OrderType::class);
	    $form->remove('visitors');
	    $form->handleRequest($request);


	    if($form->isSubmitted() && $form->isValid()) {
		    $session = $request->getSession();
		    //$session->clear();

		    //$cart = $session->get('cart',[]);
		    $session->set('cart', $form->getData());
		    //dd($session->get('cart')->getVisitorsNbr());

		    $this->addFlash('success', 'Veuillez entrer les détails pour vos billets');
			return $this->redirectToRoute('visitors');
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
	    $session = $request->getSession();
	    $form = $this->createForm(OrderType::class);
	    $form->remove('visitorsNbr')
	         ->remove('fullDay')
	         ->remove('reservedFor');
	    $booking = $session->get('cart');
	    $visitorsNbr = $booking->getVisitorsNbr();
	    $form->handleRequest($request);


	    if($form->isSubmitted() && $form->isValid()) {
		    $this->addFlash('success', 'Veuillez entrer les détails pour vos billets');

		    return $this->redirectToRoute('booking/ticket/cart');
	    }

	    return $this->render('pages/booking/visitors.html.twig', [
		    'current_menu'  => 'Booking',
		    'form'          => $form->createView(),
		    'visitorsNbr'  => $visitorsNbr
	    ]);

    }
}
