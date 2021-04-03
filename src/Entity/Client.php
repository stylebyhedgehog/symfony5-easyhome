<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="Ad", mappedBy="owner")
     */
    private $posted_ads;

    /**
     * @ORM\OneToMany(targetEntity="Ad", mappedBy="agent")
     */
    private $controlled_ads;

    /**
     * @ORM\OneToMany(targetEntity="Favorite", mappedBy="renter")
     */
    private $rented_ads;

    /**
     * @ORM\OneToMany(targetEntity="Favorite", mappedBy="client")
     */
    private $favorite_ads;
    public function __construct() {

        $this->controlled_ads = new ArrayCollection();
        $this->posted_ads = new ArrayCollection();
        $this->rented_ads = new ArrayCollection();
        $this->favorite_ads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function addRoles(string $role): self
    {
        $current_roles=$this->getRoles();
        array_push($current_roles, $role);
        $this->setRoles($current_roles);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getPostedAds(): Collection
    {
        return $this->posted_ads;
    }

    public function addPostedAd(Ad $postedAd): self
    {
        if (!$this->posted_ads->contains($postedAd)) {
            $this->posted_ads[] = $postedAd;
            $postedAd->setOwner($this);
        }

        return $this;
    }

    public function removePostedAd(Ad $postedAd): self
    {
        if ($this->posted_ads->removeElement($postedAd)) {
            // set the owning side to null (unless already changed)
            if ($postedAd->getOwner() === $this) {
                $postedAd->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getControlledAds(): Collection
    {
        return $this->controlled_ads;
    }

    public function addControlledAd(Ad $controlledAd): self
    {
        if (!$this->controlled_ads->contains($controlledAd)) {
            $this->controlled_ads[] = $controlledAd;
            $controlledAd->setAgent($this);
        }

        return $this;
    }

    public function removeControlledAd(Ad $controlledAd): self
    {
        if ($this->controlled_ads->removeElement($controlledAd)) {
            // set the owning side to null (unless already changed)
            if ($controlledAd->getAgent() === $this) {
                $controlledAd->setAgent(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->getUsername();
    }

    /**
     * @return Collection|Ad[]
     */
    public function getRentedAds(): Collection
    {
        return $this->rented_ads;
    }

    public function addRentedAd(Ad $rentedAd): self
    {
        if (!$this->rented_ads->contains($rentedAd)) {
            $this->rented_ads[] = $rentedAd;
            $rentedAd->setRenter($this);
        }

        return $this;
    }

    public function removeRentedAd(Ad $rentedAd): self
    {
        if ($this->rented_ads->removeElement($rentedAd)) {
            // set the owning side to null (unless already changed)
            if ($rentedAd->getRenter() === $this) {
                $rentedAd->setRenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Favorite[]
     */
    public function getFavoriteAds(): Collection
    {
        return $this->favorite_ads;
    }

    public function addFavoriteAd(Favorite $favoriteAd): self
    {
        if (!$this->favorite_ads->contains($favoriteAd)) {
            $this->favorite_ads[] = $favoriteAd;
            $favoriteAd->setClient($this);
        }

        return $this;
    }

    public function removeFavoriteAd(Favorite $favoriteAd): self
    {
        if ($this->favorite_ads->removeElement($favoriteAd)) {
            // set the owning side to null (unless already changed)
            if ($favoriteAd->getClient() === $this) {
                $favoriteAd->setClient(null);
            }
        }

        return $this;
    }



}
