<?php
namespace App\Controller;

use App\Service\Cart\StripeClient;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentController extends AbstractController
{
	/**
	 * @Route("/booking/payment", name="payment")
	 * @param Request      $request
	 *
	 * @param StripeClient $stripeClient
	 *
	 * @return Response
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function payment(Request $request, StripeClient $stripeClient): Response
	{
		$form = $this->paymentForm();

		if ($request->isMethod('POST')) {
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				try {
					$charge = $stripeClient->chargeClient($form->getData());

					return $this->redirectToRoute('success', [
						'receipt_email' => $charge->receipt_email,
						'amount' => ($charge->amount/100),
					]);
				} catch (ApiErrorException $e) {
					$this->addFlash('warning', sprintf('Unable to take payment, %s', $e instanceof ApiErrorException ? lcfirst($e->getMessage()) : 'please try again.'));
				}

			}
		}
		return $this->render('pages/booking/payment.html.twig', [
			'current_menu'  => 'Booking',
			'form' => $form->createView(),
			'stripe_public_key' => $this->getParameter('stripe_public_key'),
		]);
	}

	private function paymentForm(): Form
	{
		return $this->get('form.factory')
		            ->createNamedBuilder('payment-form')
					/*->add('lastname', TextType::class,[
						'constraints' => [new NotBlank(), new Length([
							'min'=>2,'max'=> 20])],
						'attr' => [
							'value' => 'Boss',
							'placeholder' => 'Last name'
						]
					])
					->add('firstname', TextType::class,[
						'constraints' => [new NotBlank(), new Length([
							 'min'=>2,'max'=> 20])],
						'attr' => [
							'value' => 'Big',
							'placeholder' => 'First name'
						]
					])*/
					->add('email', EmailType::class,[
						'constraints' => [new NotBlank(), new Email()],
						'attr' => [
							'value' => 'BigBoss@boss.de',
							'placeholder' => 'Email'
						]
					])
				     ->add('token', HiddenType::class, [
					     'constraints' => [new NotBlank()],
				     ])
				     ->add('submit', SubmitType::class)
				     ->getForm();
	}
}