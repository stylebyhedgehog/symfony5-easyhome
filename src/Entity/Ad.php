<?php


namespace App\Entity;

use App\Repository\AdRepository;
use App\Service\constants\AdStatus;
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
     * @Assert\NotBlank(message="Выберите регион")
     * @ORM\Column(name="region",type="string", nullable=false, length=150)
     */
    private $region;
    /**
     * @Assert\NotBlank(message="Выберите город")
     * @ORM\Column(name="city",type="string", nullable=false, length=150)
     */
    private $city;

    /**
     * @ORM\Column(name="district",type="string", nullable=true, length=150)
     */
    private $district;

    /**
     * @Assert\NotBlank(message="Выберите тип")
     * @ORM\Column(name="street_type",type="string", nullable=false, length=150)
     */
    private $street_type;

    /**
     * @Assert\NotBlank(message="Заполните поле")
     * @ORM\Column(name="street",type="string", nullable=false, length=150)
     */
    private $street;

    /**
     * @Assert\NotBlank(message="Заполните поле")
     * @Assert\Regex("(\d+[a-z/\d]*)", message="формат")
     * @ORM\Column(name="house_number",type="string", nullable=false, length=150)
     */
    private $house_number;

    /**
     * @Assert\Regex("(\d+[a-z/\d]*)", message="формат")
     * @ORM\Column(name="flat_number",type="string", nullable=true)
     */
    private $flat_number;

    /**
     * @Assert\NotBlank(message="Введите площадь")
     * @Assert\Regex("/^[0-9]*[.]?[0-9]+$/", message="Неправильный формат")
     * @ORM\Column(name="sqr",type="string", nullable=false)
     */
    private $sqr;

    /**
     * @Assert\NotBlank(message="Добавьте описание")
     * @ORM\Column(name="description",type="text", nullable=false)
     */
    private $description;

    /**
     * @Assert\NotBlank(message="Добавьте стоимость")
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
     * @ORM\Column(name="status",type="integer", nullable=false)
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

    /**
     * @ORM\OneToMany(targetEntity="Application", mappedBy="ad")
     */
    private $applications;

    /**
     * @ORM\OneToMany(targetEntity="BrowsingHistory", mappedBy="ad")
     */
    private $browsing_history;

    /**
     * @Assert\NotBlank(message="Выберите тип")
     * @ORM\Column(name="type_rent",type="string", nullable=false)
     */
    private $type_rent;
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->browsing_history = new ArrayCollection();
        $this->create_date = new DateTime();
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
        return  AdStatus::$status_get[$this->status];
    }
    public function getStatusNumber(): ?int
    {
        return  $this->status;
    }

    public function setStatus(int $status): self
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

    public function getSqr(): ?string
    {
        return $this->sqr;
    }

    public function setSqr(string $sqr): self
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

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setAd($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getAd() === $this) {
                $application->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BrowsingHistory[]
     */
    public function getBrowsingHistory(): Collection
    {
        return $this->browsing_history;
    }

    public function addBrowsingHistory(BrowsingHistory $browsingHistory): self
    {
        if (!$this->browsing_history->contains($browsingHistory)) {
            $this->browsing_history[] = $browsingHistory;
            $browsingHistory->setAd($this);
        }

        return $this;
    }

    public function removeBrowsingHistory(BrowsingHistory $browsingHistory): self
    {
        if ($this->browsing_history->removeElement($browsingHistory)) {
            // set the owning side to null (unless already changed)
            if ($browsingHistory->getAd() === $this) {
                $browsingHistory->setAd(null);
            }
        }

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(string $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getStreetType(): ?string
    {
        return $this->street_type;
    }

    public function setStreetType(?string $street_type): self
    {
        $this->street_type = $street_type;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->house_number;
    }

    public function setHouseNumber(?string $house_number): self
    {
        $this->house_number = $house_number;

        return $this;
    }

    public function getFlatNumber(): ?string
    {
        return $this->flat_number;
    }

    public function setFlatNumber(?string $flat_number): self
    {
        $this->flat_number = $flat_number;

        return $this;
    }

    public function getTypeRent(): ?string
    {
        return $this->type_rent;
    }

    public function setTypeRent(string $type_rent): self
    {
        $this->type_rent = $type_rent;

        return $this;
    }




}