<?php

namespace App\Controller;

use App\Entity\Booking;
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
    public function index(Request $request): Response
    {
    	$form = $this->createForm(OrderType::class);
	    dump($form);
    	$form->handleRequest($request);

	    //if($form->isSubmitted() && $form->isValid()) {


		    return new Response('Genug für Heute, Morgen wieder!!!!');
	    //}


        return $this->render('pages/booking/order.html.twig', [
	        'current_menu'  => 'Booking',
	        'form'          => $form->createView()
        ]);
    }
}
