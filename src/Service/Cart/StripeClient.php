<?php
namespace App\Service\Cart;

use App\Entity\PaymentCard;
use App\Repository\PaymentCardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeClient
{
	private $newCustomer;
	private $config;
	private $em;
	private $secretKey;
	private $cartService;
	private $paymentCardRepository;

	/**
	 * StripeClient constructor.
	 *
	 * @param                        $secretKey
	 * @param array                  $config
	 * @param EntityManagerInterface $em
	 * @param CartService            $cartService
	 * @param PaymentCardRepository  $repository
	 */
	public function __construct($secretKey, array $config, EntityManagerInterface $em, CartService $cartService, PaymentCardRepository $repository)
	{
		Stripe::setApiKey($secretKey);
		$this->config = $config;
		$this->em = $em;
		$this->secretKey = $secretKey;
		$this->cartService = $cartService;
		$this->paymentCardRepository = $repository;
	}

	/**
	 * @param array $data
	 *
	 * @return Charge
	 * @throws ApiErrorException
	 * @throws NonUniqueResultException
	 * @throws \Exception
	 */
	public function chargeClient(array $data)
	{
		//get Card if exist
		$paymentCard = $this->paymentCardRepository->findCard($data['token']);
		if(empty($paymentCard)) {
			try {
				//create new Costumer
				$customer = Customer::create([
					                             'source' => $data['token'],
					                             'email' => $data['email'],
				                             ]
				);
				$data['customerId'] = $customer->id;
				$paymentCard = new PaymentCard($data);
				$this->newCustomer = true;

			} catch(ApiErrorException $e) {
				throw $e;
			}

		}else {
			$data['customerId'] = $paymentCard->getCustomerId();
			$this->newCustomer = false;
		}

		//Charge Costumer
		try{
			$charge = $this->chargeClientId($data);
		} catch(ApiErrorException $e) {
			throw $e;
		}
		$this->savePayment($paymentCard);
		return $charge;
	}

	/**
	 * @param array $data
	 *
	 * @return Charge
	 * @throws ApiErrorException
	 */
	private function chargeClientId(array $data)
	{
			return Charge::create([
				                         'amount' => $this->config['decimal'] ? $this->cartService->getLastPrice() * 100 : $this->cartService->getLastPrice(),
				                         'currency' => $this->config['currency'],
				                         'description' => 'Buchung Erfolgreich',
				                         'customer' => $data['customerId'],
				                         'receipt_email' => $data['email'],
			                         ]
			);
	}

	/**
	 * @param PaymentCard $paymentCard
	 *
	 * @throws \Exception
	 */
	private function savePayment(PaymentCard $paymentCard)
	{
		foreach($this->cartService->getCart() as $booking){
			foreach($booking->getVisitors() as $visitor){
				$visitor->setTicketCode(bin2hex(random_bytes(15)) . $visitor->getId());
				$visitor->setBooking($booking);
			}
			$paymentCard->addBooking($booking);
			if(!$this->newCustomer){
				$this->em->persist($booking);
				$this->em->flush();
			}
		}
		if(!$this->newCustomer){
			$this->em->persist($paymentCard);
			$this->em->flush();
		}
	}

}