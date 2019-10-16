<?php
/**
 * Created by IntelliJ IDEA.
 * User: BigBoss
 * Date: 03/10/2019
 * Time: 13:22
 */

namespace App\Controller;


use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

	/**
	 * @Route("/", name="home")
	 * @param CartService $cartService
	 *
	 * @return Response
	 */
	public function home(CartService $cartService): Response
	{
		return $this->render('pages/home.html.twig',[
			'current_menu' => 'Home',
			'cart'          => $cartService->fullCart()
		]);
	}
}