<?php

namespace App\Service\Cart;


class CalculatorTicketsPrice
{
	/**
	 * Reduction from ticket price
	 */
	const REDUCTION = 10;
	/**
	 * ticket [type=>[Age=>['less_than' = NULL],price]
	 */
	const TICKET_PRICE = [
		'BABY'   => [
			'AGE' => 0,
			'PRICE' => 0
		],
		'KID'    => [
			'AGE'  => 4,
			'PRICE' => 8
		],
		'NORMAL' => [
			'AGE' => 12,
			'PRICE' => 16
		],
		'SENIOR' => [
			'AGE' => 60,
			'PRICE' => 12
		]
	];


	/**
	 * Calculate age and set ticket price in session for every visitor
	 *
	 * @param CartService $cartService
	 *
	 * @return int
	 */
	public function setPriceTicket(CartService $cartService)
	{
		$cartInfo = $cartService->getCartInfo();
		$lastPrice = 0;
		foreach($cartInfo['orders'] as $key => $order){
			$visitedDate = $order->getReservedFor();
			if($order->getTotalPrice()) {
				$lastPrice += $order->getTotalPrice();
				continue;
			}
			foreach ($order->getVisitors() as $visitor) {
				if($visitor->getTicketAmount()) continue;
				$visitor->getBirthday();//DateTIME
				$ageInDays = date_diff($visitor->getBirthday(), $visitedDate);
				$ageInYears = $ageInDays->format("%R%a")/365;
				$ticketPrice = $this->ticketPrice($ageInYears, $visitor->hasDiscount());
				$visitor->setTicketAmount($ticketPrice);
			}
			$order->setTotalPrice();
			$lastPrice += $order->getTotalPrice();
		}
		return $lastPrice;
	}

	/**
	 * Get matched ticket price with age and discount
	 * @param int  $age
	 * @param bool $hasDiscount
	 *
	 * @return int
	 */
	private function ticketPrice(int $age, bool $hasDiscount): int
	{
		$discount = 0;
		if($hasDiscount && $age >= self::TICKET_PRICE['NORMAL']['AGE'])
			$discount = self::REDUCTION;

		if($age < self::TICKET_PRICE['KID']['AGE'])
			return self::TICKET_PRICE['BABY']['PRICE'] ;

		if($age >= self::TICKET_PRICE['KID']['AGE']
				&& $age < self::TICKET_PRICE['NORMAL']['AGE'])
			return self::TICKET_PRICE['KID']['PRICE'];

		if($age >= self::TICKET_PRICE['NORMAL']['AGE']
				&& $age < self::TICKET_PRICE['SENIOR']['AGE'])
			return self::TICKET_PRICE['NORMAL']['PRICE'] - $discount;

		if($age >= self::TICKET_PRICE['SENIOR']['AGE'])
			return self::TICKET_PRICE['SENIOR']['PRICE'] - $discount;
	}
}