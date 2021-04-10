<?php


namespace App\Entity;

use App\Repository\AdRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AdRepository::class)
 */
class Ad
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="city",type="string", nullable=false, length=150)
     */
    private $city;

    /**
     * @ORM\Column(name="address",type="string", nullable=false, length=150)
     */
    private $address;

    /**
     * @ORM\Column(name="flat",type="integer", nullable=false)
     */
    private $flat;

    /**
     * @ORM\Column(name="sqr",type="integer", nullable=false)
     */
    private $sqr;

    /**
     * @ORM\Column(name="description",type="text", nullable=false)
     */
    private $description;

    /**
     * @ORM\Column(name="price",type="integer", nullable=false)
     */
    private $price;

    /**
     * @ManyToOne(targetEntity="Client",inversedBy="posted_ads")
     * @JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $owner;

    /**
     * @ManyToOne(targetEntity="Client", inversedBy="controlled_ads")
     * @JoinColumn(name="agent_id", referencedColumnName="id")
     */
    private $agent;

    /**
     * @ManyToOne(targetEntity="Client", inversedBy="rented_ads")
     * @JoinColumn(name="renter_id", referencedColumnName="id", nullable=true)
     */
    private $renter;
    /**
     * @ORM\Column(name="status",type="string", nullable=false, length=30)
     */
    private $status;

    /**
     * @ORM\Column(name="create_date",type="datetime", nullable=false)
     */
    private $create_date;

    /**

     * @ORM\Column(name="update_date",type="datetime", nullable=false)
     */
    private $update_date;

    /**
     * @ORM\OneToMany(targetEntity="AdImage", mappedBy="ad", cascade={"persist"})
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getFlat(): ?int
    {
        return $this->flat;
    }

    public function setFlat(int $flat): self
    {
        $this->flat = $flat;

        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }



    public function getOwner(): ?Client
    {
        return $this->owner;
    }

    public function setOwner(?Client $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAgent(): ?Client
    {
        return $this->agent;
    }

    public function setAgent(?Client $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getRenter(): ?Client
    {
        return $this->renter;
    }

    public function setRenter(?Client $renter): self
    {
        $this->renter = $renter;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->create_date;
    }

    public function setCreateDate(\DateTimeInterface $create_date): self
    {
        $this->create_date = $create_date;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(\DateTimeInterface $update_date): self
    {
        $this->update_date = $update_date;

        return $this;
    }

    public function getSqr(): ?int
    {
        return $this->sqr;
    }

    public function setSqr(int $sqr): self
    {
        $this->sqr = $sqr;

        return $this;
    }

    /**
     * @return Collection|AdImage[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(AdImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(AdImage $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }




}