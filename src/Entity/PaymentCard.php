<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentCardRepository")
 */
class PaymentCard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $chargeId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $customerId;

	/**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="paymentCard", orphanRemoval=true, cascade={"persist"})
     */
    private $bookings;


	public function __construct(Array $data)
	{
	   $this->bookings = new ArrayCollection();
	   $this->chargeId = $data['token'];
	   $this->email = $data['email'];
	   $this->customerId = $data['customerId'];
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChargeId(): ?string
    {
        return $this->chargeId;
    }

    public function setChargeId(?string $chargeId): self
    {
        $this->chargeId = $chargeId;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setPaymentCard($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getPaymentCard() === $this) {
                $booking->setPaymentCard(null);
            }
        }

        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }
}
