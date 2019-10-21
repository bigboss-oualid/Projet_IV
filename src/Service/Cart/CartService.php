<?php

namespace App\Service\Cart;

use App\Entity\Booking;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
	protected $session;

	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
	}

	/**
	 * @param Booking $order
	 */
	public function addOrder(Booking $order)
	{
		$cart = $this->session->get('cart', []);
		array_push($cart ,$order);
		$this->session->set('cart', $cart);
	}

	/**
	 * @param Booking $order
	 *
	 */
	public function saveTickets(Booking $order){

		$cart = $this->session->get('cart', []);

		end($cart)->addVisitors($order->getVisitors());

		$this->session->set('cart', $cart);
	}

	/**
	 * Delete from cart Order without Ticket
	 *
	 * @return array
	 */
	public function getCartInfo() :array
	{
		$cart = $this->session->get('cart', []);
		if(empty($cart)) return [];
		$lastPrice = 0;
		$totalVisitorNbr = 0;
		foreach($cart as $key => $booking){
			if($booking->getVisitors()->isEmpty()){
				unset($cart[$key]);
			}
			else{
				$booking->setTotalPrice();
				$lastPrice += $booking->getTotalPrice();
				$totalVisitorNbr += count($booking->getVisitors());
			}
		}
		$cart = array_values($cart);
		$this->session->set('cart', $cart);
		return [
			'cart' => $cart,
			'last_price'=> $lastPrice,
			'total_visitor_nbr' => $totalVisitorNbr
		];
	}

	/**
	 * @return Booking
	 */
	public function getLastOrder(): Booking
	{
		$cart = $this->session->get('cart', []);
		$order = end($cart);
		if(!empty($order)){
			return $order;
		}
		return new Booking();
	}

	/**
	 * @param int $idOrder
	 *
	 * @return \DateTime
	 */
	public function deleteOrder(int $idOrder): \DateTime
	{
		$cart = $this->session->get('cart', []);
		$reservedFor = $cart[$idOrder-1]->getReservedFor();
		unset($cart[$idOrder-1]);
		$this->session->set('cart', $cart);
		return $reservedFor;
	}

	/**
	 * @param int $idOrder
	 * @param int $idVisitor
	 *
	 * @return array
	 */
	public function deleteTicket(int $idOrder, int $idVisitor): array
	{
		$cart = $this->session->get('cart', []);
		$order = &$cart[$idOrder - 1];
		$reservedFor = $order->getReservedFor();
		$name = $order->getVisitors()[$idVisitor - 1]->getLastName();

		$order->getVisitors()->remove($idVisitor - 1);
		if (!$order->getVisitors()->isEmpty()) {
			$items = new ArrayCollection();
			//Reindex array of visitors
			foreach ($order->getVisitors() as $item) {
				$items->add($item);
			}
			$order->addVisitors($items);
			//reduce visitor number
			$order->setVisitorsNbr($order->getVisitorsNbr()-1);
		}
		$this->session->set('cart', $cart);
		return[
			'name' => $name,
			'reserved_for' => $reservedFor
		];
	}

	public function clean()
	{
		$this->session->remove('cart');
	}

	public function fullCart() {
		if(empty($this->session->get('cart'))){
			return null;
		}
		return 'full';
	}
}