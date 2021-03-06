<?php

namespace App\Entity;

use App\Repository\BrowsingHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=BrowsingHistoryRepository::class)
 */
class BrowsingHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ManyToOne(targetEntity="Client", inversedBy="browsing_history")
     * @JoinColumn(name="client_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $client;
    /**
     * @ManyToOne(targetEntity="Ad", inversedBy="browsing_history")
     * @JoinColumn(name="ad_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $ad;

    /**
     * @ORM\Column(name="create_date",type="datetime", nullable=false)
     */
    private $create_date;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->create_date;
    }

    public function setCreateDate(\DateTimeInterface $create_date): self
    {
        $this->create_date = $create_date;

        return $this;
    }
}
