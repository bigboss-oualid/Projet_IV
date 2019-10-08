<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{

	const TYPE_TICKET= [
		'day'      => 1,
		'half day' => 0,
	];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="object")
     */
    private $client;

    /**
     * @ORM\Column(type="array")
     */
    private $visitors = [];

	/**
	 * @ORM\Column(type="integer")
	 */
    private $visitorsNbr;

    /**
     * @ORM\Column(type="datetime")
     */
    private $reservedFor;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fullDay;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalPrice;


    public function __construct()
    {
    	$this->setCreatedAt(new \DateTime());
    }

	public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient($client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getVisitors(): ?array
    {
        return $this->visitors;
    }

    public function setVisitors(array $visitors): self
    {
        $this->visitors = $visitors;

        return $this;
    }


	public function getVisitorsNbr()
	{
		return $this->visitorsNbr;
	}

	public function setVisitorsNbr($visitorsNbr)
	{
		$this->visitorsNbr = $visitorsNbr;
		return $this;
	}

    public function getReservedFor(): ?\DateTimeInterface
    {
        return $this->reservedFor;
    }

    public function setReservedFor(\DateTimeInterface $reservedFor): self
    {
        $this->reservedFor = $reservedFor;

        return $this;
    }

    public function getFullDay(): ?bool
    {
        return $this->fullDay;
    }

    public function setFullDay(bool $fullDay): self
    {
        $this->fullDay = $fullDay;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function getFormattedPrice(): string
    {
    	return number_format($this->totalPrice, 0, '', ' ');
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }
}
