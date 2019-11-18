<?php

namespace App\Service\Cart;

use App\Entity\Booking;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
	protected $session;
	/**
	 * @var Booking []
	 */
	private $cart;

	/**
	 * @var int
	 */
	private $lastPrice;

	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
		$this->cart = $this->session->get('cart', []);
		$this->lastPrice = $this->session->get('lastPrice', []);
	}

	/**
	 * @param Booking $order
	 */
	public function addOrder(Booking $order)
	{
		array_push($this->cart ,$order);
		$this->session->set('cart', $this->cart);
	}

	/**
	 * Delete from cart Order without Ticket
	 *
	 * @return array
	 */
	public function getCartInfo() :array
	{
		if(empty($this->cart)) return [];
		$totalVisitorNbr = 0;
		foreach($this->cart as $key => $booking){
			$totalVisitorNbr += count($booking->getVisitors());
		}
		$this->cart = array_values($this->cart);
		$this->session->set('cart', $this->cart);
		return [
			'orders' => $this->cart,
			'total_visitor_nbr' => $totalVisitorNbr
		];
	}

	/**
	 * @return Booking
	 */
	public function getLastOrder(): Booking
	{
		$order = end($this->cart);
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
		$reservedFor = $this->cart[$idOrder-1]->getReservedFor();
		unset($this->cart[$idOrder-1]);
		$this->session->set('cart', $this->cart);
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
		$order = &$this->cart[$idOrder - 1];
		$reservedFor = $order->getReservedFor();
		$name = $order->getVisitors()[$idVisitor - 1]->getLastName();

		$order->getVisitors()->remove($idVisitor - 1);
		if (!$order->getVisitors()->isEmpty()) {
			$newVisitorsList = new ArrayCollection();
			//Reindex array of visitors
			foreach ($order->getVisitors() as $visitor) {
				$newVisitorsList->add($visitor);
			}
			$order->addVisitors($newVisitorsList);
			//reduce visitor number
			$order->setVisitorsNbr($order->getVisitorsNbr()-1);
		}
		else {
			$this->deleteOrder($idOrder);
		}
		$this->session->set('cart', $this->cart);
		return[
			'name' => $name,
			'reserved_for' => $reservedFor
		];
	}

	public function clean()
	{
		$this->session->clear();
	}

	/**
	 * delete $cart without visitor
	 */
	public function refresh()
	{
		foreach($this->cart as $key => $booking){
			if($booking->getVisitors()->isEmpty()){
				unset($this->cart[$key]);
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getCart()
	{
		return $this->cart;
	}

	/**
	 * @return int
	 */
	public function getLastPrice(): int
	{
		return $this->lastPrice;
	}

	public function setLastPrice(int $price)
	{
		$this->session->set('lastPrice', $price);
	}

}