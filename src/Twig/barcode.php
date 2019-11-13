<?php

namespace App\Twig;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\Exceptions\BarcodeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class barcode extends AbstractExtension
{
	public function getFilters()
	{
		return [
			new TwigFilter('barcode', [$this, 'generateBarcode']),
		];
	}

	/**
	 * @param string $codeTicket
	 *
	 * @return string
	 */
	public function generateBarcode(string $codeTicket)
	{
		$generator = new BarcodeGeneratorPNG();
		try {
			return base64_encode($generator->getBarcode($codeTicket, $generator::TYPE_CODE_93));
		} catch(BarcodeException $e) {
			return $e;
		}
	}
}