<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Le nom est nécessaires",groups={"visitor"}),
     * @Assert\Length(
     *     min="2", max="50",
     *      minMessage = "Le nom doit avoir au minimum {{ limit }} characters",
     *      maxMessage = "Le nom doit avoir au maximum {{ limit }} characters",
     *     groups={"visitor"}
     *     )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Le prénom est nécessaires",groups={"visitor"}),
     * @Assert\Length(
     *     min="2", max="50",
     *      minMessage = "Le prénom doit avoir au maximum {{ limit }} characters",
     *      maxMessage = "Le prénom doit avoir au maximum {{ limit }} characters",
     *     groups={"visitor"}
     *     )
     */
    private $firstName;

	/**
	 * @ORM\Column(type="string", length=70)
	 * @Assert\Country(groups={"visitor"})
	 */
	private $country;

	/**
	 * @ORM\Column(type="date")
	 * @Assert\NotBlank(groups={"visitor"}, message="birthday is necessary")
	 * @Assert\Date(groups={"visitor"})
	 * @Assert\LessThanOrEqual("today", groups={"visitor"})
	 */
	private $birthday;

	/**
     * @ORM\Column(type="boolean")
     * @Assert\Choice(
     *     choices = { true, false },groups={"visitor"}
     * )
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
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id", nullable=false)
     */
    private $booking;

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

	public function setBirthday( $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

	public function hasDiscount(): ?bool
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
	    $booking->addVisitor($this);
	    $this->booking = $booking;

        return $this;
    }
}
