<?php
namespace App\Service\Cart;


use App\Entity\Buyer;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Charge;
use Stripe\Error\Base;
use Stripe\Stripe;

class StripeClient
{
	private $config;
	private $em;
	private $secretKey;
	private $cartService;

	/**
	 * StripeClient constructor.
	 *
	 * @param                        $secretKey
	 * @param array                  $config
	 * @param EntityManagerInterface $em
	 * @param CartService            $cartService
	 * @param Buyer                  $buyer
	 */
	public function __construct($secretKey, array $config, EntityManagerInterface $em, CartService $cartService, Buyer  $buyer)
	{
		Stripe::setApiKey($secretKey);
		$this->config = $config;
		$this->em = $em;
		$this->secretKey = $secretKey;
		$this->cartService = $cartService;
	}
	/*
	 *  "lastname" => "Boss"
		"firstname" => "Big"
		"email" => "BigBoss@boss.de"
		"token" => "tok_1FXaTxByJQDotcYni7aojPPc"
	 */

	public function chargeClient(array $data)
	{
		dump($data);
		$data2 = [
			'source' => $data['token'],
			'email' => $data['email'],
		];
		try {
			$charge = Charge::create([
				'amount' => $this->config['decimal'] ? $this->config['premium_amount'] * 100 : $this->config['premium_amount'],
                'currency' => $this->config['currency'],
                'description' => 'Premium blog access',
                'source' => $token,
                'receipt_email' => $user->getEmail(),
				                         ]);
		} catch (Base $e) {
			$this->logger->error(sprintf('%s exception encountered when creating a premium payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);

			throw $e;
		}

		$user->setChargeId($charge->id);
		$user->setPremium($charge->paid);
		$this->em->flush();
	}

}