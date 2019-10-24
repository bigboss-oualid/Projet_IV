<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @CustomAssert\ConstraintsVisitDate(groups={"order"})
 */
class Booking
{

	const FULL_DAY_TICKET = [
        'Day'      => true,
        'Half day' => false,
	];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
	 * @ORM\Column(type="integer")
	 * @Assert\NotBlank(groups={"order"})
	 */
    private $visitorsNbr;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(groups={"order"})
     * @Assert\Date(groups={"order"})
     * @Assert\GreaterThanOrEqual("today", groups={"visitor"})
     */
    private $reservedFor;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Choice(
     *     choices = { true, false},groups={"order"}
     * )
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

	/**
	 * @var Visitor|null
	 */
    private $visitor;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Visitor", mappedBy="booking", orphanRemoval=true, cascade={"persist"})
	 * @Assert\Valid(groups={"visitor"})
	 */
	private $visitors;

	/**
	 * Booking constructor.
	 */
	public function __construct()
    {
    	$this->setCreatedAt(new \DateTime());
        $this->visitors = new ArrayCollection();
    }

	public function getId(): ?int
	{
	     return $this->id;
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

    public function setReservedFor( $reservedFor): self
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

    public function setCreatedAt( $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

	/**
	 * Define price format ex: '2 479'
	 * @return string
	 */
    public function getFormattedPrice(): string
    {
    	return number_format($this->totalPrice, 0, '', ' ');
    }

    public function setTotalPrice(): self
    {
	    $this->totalPrice = 0;
    	foreach($this->visitors as $visitor ){
		    $this->totalPrice += $visitor->getTicketAmount();
	    }


        return $this;
    }

	/**
	 * @return Collection|Visitor[]
	 */
	public function getVisitors(): Collection
	{
		return $this->visitors;
	}

	/**
	 * @param  Collection|Visitor[]
	 *
	 * @return Collection|Visitor[]
	 */
	public function addVisitors(ArrayCollection  $visitors): Collection
	{
		$this->visitors = clone $visitors;
		return $this->visitors;
	}

	/**
	 * @return Visitor|null
	 */
	public function getVisitor(): ?Visitor
	{
		return $this->visitor;
	}

	/**
	 * @param Visitor|null $visitor
	 *
	 * @return Booking
	 */
	public function setVisitor(?Visitor $visitor): self
	{
		$this->visitor = $visitor;
		return $this;
	}

	public function addVisitor(Visitor $visitor): self
    {
        if (!$this->visitors->contains($visitor)) {
            $this->visitors[] = $visitor;
            $visitor->setBooking($this);
        }

        return $this;
    }

    public function removeVisitor(Visitor $visitor): self
    {
        if ($this->visitors->contains($visitor)) {
            $this->visitors->removeElement($visitor);
            // set the owning side to null (unless already changed)
            if ($visitor->getBooking() === $this) {
                $visitor->setBooking(null);
            }
        }

        return $this;
    }
}
