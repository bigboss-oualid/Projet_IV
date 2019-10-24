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
			'AGE' => ['LESS_THAN' => 4 ]
			, 'PRICE' => 0
		],
		'KID'    => [
			'AGE' => ['LESS_THAN' => 12],
			'PRICE' => 8
		],
		'NORMAL' => [
			'AGE' => ['LESS_THAN' => 60],
			'PRICE' => 16
		],
		'SENIOR' => [
			'PRICE' => 12
		]
	];


	/**
	 * Calculate age and set ticket price in session for every visitor
	 *
	 * @param CartService $cartService
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
				$normalPrice = $this->ticketPrice($ageInYears, $visitor->hasDiscount());
				$visitor->setTicketAmount($normalPrice);
			}
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
		if($hasDiscount && $age > self::TICKET_PRICE['BABY']['AGE']['LESS_THAN'])
			$discount = self::REDUCTION;

		if($age < self::TICKET_PRICE['BABY']['AGE']['LESS_THAN'])
			return self::TICKET_PRICE['BABY']['PRICE'] ;

		if($age >= self::TICKET_PRICE['BABY']['AGE']['LESS_THAN']
				&& $age < self::TICKET_PRICE['KID']['AGE']['LESS_THAN'])
			return self::TICKET_PRICE['KID']['PRICE'] - $discount;

		if($age >= self::TICKET_PRICE['KID']['AGE']['LESS_THAN']
				&& $age < self::TICKET_PRICE['NORMAL']['AGE']['LESS_THAN'])
			return self::TICKET_PRICE['NORMAL']['PRICE'] - $discount;

		if($age >= self::TICKET_PRICE['NORMAL']['AGE']['LESS_THAN'])
			return self::TICKET_PRICE['SENIOR']['PRICE'] - $discount;
	}
}