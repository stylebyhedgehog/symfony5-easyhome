<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
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
     * @ORM\OneToMany(targetEntity="Favorite", mappedBy="client")
     */
    private $favorites;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="rater")
     */
    private $posted_reviews;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="client")
     */
    private $client_reviews;

    /**
     * @ORM\OneToMany(targetEntity="Application", mappedBy="sender")
     */
    private $applications_sent;

    /**
     * @ORM\OneToMany(targetEntity="Application", mappedBy="owner")
     */
    private $applications_incoming;

    /**
     * @ORM\OneToMany(targetEntity="Application", mappedBy="agent")
     */
    private $applications_controlled;

    /**
     * @OneToOne(targetEntity="PersonalData", mappedBy="client")
     */
    private $personal_data;

    /**
     * @ORM\OneToMany(targetEntity="BrowsingHistory", mappedBy="client")
     */
    private $browsing_history;

    public function __construct() {

        $this->controlled_ads = new ArrayCollection();
        $this->posted_ads = new ArrayCollection();
        $this->posted_reviews = new ArrayCollection();
        $this->client_reviews = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->applications_sent = new ArrayCollection();
        $this->applications_incoming = new ArrayCollection();
        $this->applications_controlled = new ArrayCollection();
        $this->browsing_history = new ArrayCollection();
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
        return $this->getUsername();
    }

    /**
     * @return Collection|Ad[]
     */
    public function getFavoriteAds(): Collection
    {
        $ads_collection=new ArrayCollection();
        foreach ($this->favorites as $favorite){
            $ads_collection->add( $favorite->getAd());
        }
        return $ads_collection;
    }
    

    /**
     * @return Collection|Review[]
     */
    public function getPostedReviews(): Collection
    {
        return $this->posted_reviews;
    }

    public function addPostedReview(Review $postedReview): self
    {
        if (!$this->posted_reviews->contains($postedReview)) {
            $this->posted_reviews[] = $postedReview;
            $postedReview->setRater($this);
        }

        return $this;
    }

    public function removePostedReview(Review $postedReview): self
    {
        if ($this->posted_reviews->removeElement($postedReview)) {
            // set the owning side to null (unless already changed)
            if ($postedReview->getRater() === $this) {
                $postedReview->setRater(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getClientReviews(): Collection
    {
        return $this->client_reviews;
    }

    public function addClientReview(Review $clientReview): self
    {
        if (!$this->client_reviews->contains($clientReview)) {
            $this->client_reviews[] = $clientReview;
            $clientReview->setClient($this);
        }

        return $this;
    }

    public function removeClientReview(Review $clientReview): self
    {
        if ($this->client_reviews->removeElement($clientReview)) {
            // set the owning side to null (unless already changed)
            if ($clientReview->getClient() === $this) {
                $clientReview->setClient(null);
            }
        }

        return $this;
    }

    public function getPersonalData(): ?PersonalData
    {
        return $this->personal_data;
    }

    public function setPersonalData(?PersonalData $personal_data): self
    {
        // unset the owning side of the relation if necessary
        if ($personal_data === null && $this->personal_data !== null) {
            $this->personal_data->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($personal_data !== null && $personal_data->getClient() !== $this) {
            $personal_data->setClient($this);
        }

        $this->personal_data = $personal_data;

        return $this;
    }

    /**
     * @return Collection|Favorite[]
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setClient($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getClient() === $this) {
                $favorite->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplicationsSent(): Collection
    {
        return $this->applications_sent;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getApplicationsSentAds(): Collection
    {
        $ads=new ArrayCollection();
        foreach ($this->applications_sent as $application){
            $ads->add($application->getAd());
        }
        return $ads;
    }

    public function addApplicationsSent(Application $applicationsSent): self
    {
        if (!$this->applications_sent->contains($applicationsSent)) {
            $this->applications_sent[] = $applicationsSent;
            $applicationsSent->setSender($this);
        }

        return $this;
    }

    public function removeApplicationsSent(Application $applicationsSent): self
    {
        if ($this->applications_sent->removeElement($applicationsSent)) {
            // set the owning side to null (unless already changed)
            if ($applicationsSent->getSender() === $this) {
                $applicationsSent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplicationsIncoming(): Collection
    {
        return $this->applications_incoming;
    }

    public function addApplicationsIncoming(Application $applicationsIncoming): self
    {
        if (!$this->applications_incoming->contains($applicationsIncoming)) {
            $this->applications_incoming[] = $applicationsIncoming;
            $applicationsIncoming->setOwner($this);
        }

        return $this;
    }

    public function removeApplicationsIncoming(Application $applicationsIncoming): self
    {
        if ($this->applications_incoming->removeElement($applicationsIncoming)) {
            // set the owning side to null (unless already changed)
            if ($applicationsIncoming->getOwner() === $this) {
                $applicationsIncoming->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplicationsControlled(): Collection
    {
        return $this->applications_controlled;
    }

    public function addApplicationsControlled(Application $applicationsControlled): self
    {
        if (!$this->applications_controlled->contains($applicationsControlled)) {
            $this->applications_controlled[] = $applicationsControlled;
            $applicationsControlled->setAgent($this);
        }
        return $this;
    }

    public function removeApplicationsControlled(Application $applicationsControlled): self
    {
        if ($this->applications_controlled->removeElement($applicationsControlled)) {
            // set the owning side to null (unless already changed)
            if ($applicationsControlled->getAgent() === $this) {
                $applicationsControlled->setAgent(null);
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
    /**
     * @return Collection|Ad[]
     */
    public function getBrowsingHistoryAd(): Collection
    {
        $ads=new ArrayCollection();
        foreach ($this->browsing_history as $item){
            $ads->add($item->getAd());
        }
        return $ads;
    }

    public function addBrowsingHistory(BrowsingHistory $browsingHistory): self
    {
        if (!$this->browsing_history->contains($browsingHistory)) {
            $this->browsing_history[] = $browsingHistory;
            $browsingHistory->setClient($this);
        }

        return $this;
    }

    public function removeBrowsingHistory(BrowsingHistory $browsingHistory): self
    {
        if ($this->browsing_history->removeElement($browsingHistory)) {
            // set the owning side to null (unless already changed)
            if ($browsingHistory->getClient() === $this) {
                $browsingHistory->setClient(null);
            }
        }

        return $this;
    }

 




}
