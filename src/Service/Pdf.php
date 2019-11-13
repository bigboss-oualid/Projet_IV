<?php

namespace App\Service;

use Pdfcrowd\HtmlToPdfClient;

class Pdf
{

	private $kernelProjectDir;
	private $userName;
	private $pdfApiKey;

	public function __construct($kernelProjectDir, $userName, $pdfApiKey)
	{
		$this->kernelProjectDir = $kernelProjectDir;
		$this->userName = $userName;
		$this->pdfApiKey = $pdfApiKey;
	}
	public function generatePDF($html){
		try
		{
			// create the API client instance
			$client = new HtmlToPdfClient($this->userName, $this->pdfApiKey);
			// create output stream for conversion result
			$output_stream = fopen($this->kernelProjectDir . "/public/pdf/newpdf2.pdf", "wb");
			// check for a file creation error
			if (!$output_stream)
			throw new \Exception(error_get_last()['message']);

			$client->setTitle('Billets');
			$client->setNoMargins(true);
			$client->setHeaderHeight("16mm");
			$client->setFooterHeight("16mm");
			$client->setHeaderHtml("<center><a class='align-center' data-pdfcrowd-placement='href-and-content'>Billets pour Musée du Louvre</a></center>");
			$client->setFooterHtml("<center><span class='pdfcrowd-page-number'></span></center>");

			$client->setOrientation('landscape');
			// run the conversion and write the result into the output stream
			$client->convertStringToStream($html, $output_stream);

		}
		catch(\Pdfcrowd\Error $why)
		{
			// report the error
			error_log("Pdfcrowd Error: {$why}\n");

			// handle the exception here or rethrow and handle it at a higher level
			throw $why;
		}

		//dd($output);
	}
}