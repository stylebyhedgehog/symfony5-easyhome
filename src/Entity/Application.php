<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use App\Service\constants\ApplicationStatus;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 */
class Application
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Client", inversedBy="applications_sent")
     * @JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;

    /**
     * @ManyToOne(targetEntity="Client", inversedBy="applications_incoming")
     * @JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ManyToOne(targetEntity="Client", inversedBy="applications_controlled")
     * @JoinColumn(name="agent_id", referencedColumnName="id")
     */
    private $agent;
    /**
     * @ManyToOne(targetEntity="Ad", inversedBy="applications")
     * @JoinColumn(name="ad_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $ad;

    /**
     * @ORM\Column (name = "status",type="integer")
     */
    private $status;

    /**
     * @ORM\Column(name="create_date",type="datetime", nullable=false)
     */
    private $create_date;


    public function __construct()
    {
        $this->create_date = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusNumber(): ?int
    {
        return $this->status;
    }
    public function getStatus(): ?string
    {
        return ApplicationStatus::$status_get[$this->status];
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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

  

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getSender(): ?Client
    {
        return $this->sender;
    }

    public function setSender(?Client $sender): self
    {
        $this->sender = $sender;

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

}
