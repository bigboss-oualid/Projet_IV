<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VisitorRepository")
 */
class Visitor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstName;

	/**
	 * @ORM\Column(type="string", length=70)
	 */
	private $country;

	/**
     * @ORM\Column(type="date")
     */
    private $birthday;

    /**
     * @ORM\Column(type="boolean")
     */
    private $discount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ticketCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $ticketAmount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Booking", inversedBy="visitors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booking;

	/**
	 * Visitor constructor.
	 */
	public function __construct()
	{
		$this->ticketAmount = 10;
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

	public function getCountry(): ?string
	{
	     return $this->country;
	}

	public function setCountry(string $country): self
    {
         $this->country = $country;

         return $this;
    }

	public function getBirthday(): ?\DateTimeInterface
	{
        return $this->birthday;
    }

	public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

	public function getDiscount(): ?bool
	{
		return $this->discount;
	}

    public function setDiscount(bool $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getTicketCode(): ?string
    {
        return $this->ticketCode;
    }

    public function setTicketCode(string $ticketCode): self
    {
        $this->ticketCode = $ticketCode;

        return $this;
    }

    public function getTicketAmount(): ?int
    {
        return $this->ticketAmount;
    }

    public function setTicketAmount(int $ticketAmount): self
    {
        $this->ticketAmount = $ticketAmount;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }
}
