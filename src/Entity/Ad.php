<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ad")
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 */
class Ad
{

    public function __construct()
    {
        $this->setDateCreated(new \DateTime());
        $this->fav_users = new ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Veuillez renseigner un titre !")
     * @Assert\Length(min="5",
     *     max="30",
     *     minMessage="5 caractères minimum !",
     *     maxMessage="30 caractères maximum !")
     *
     * @ORM\Column(type="string", length=30)
     */
    private $title;

    /**
     * @Assert\NotBlank(message = "Veuillez renseignez une description !")
     * @Assert\Length(min="10",
     *     max="50000",
     *     minMessage="10 caractères minimum !",
     *     maxMessage="50000 caractères maximum !")
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Assert\NotBlank(message = "Veuillez renseigner une ville !")
     * @Assert\Length(min="2",
     *     max="70",
     *     minMessage="2 caractères minimum !",
     *     maxMessage="70 caractères maximum !")
     *
     * @ORM\Column(type="string", length=70)
     */
    private $city;

    /**
     * @Assert\NotBlank(message = "Veuillez renseigner un code postal")
     * @Assert\Length(min="5",
     *     max="5",
     *     exactMessage="Le code postal doit être composé de 5 caractères!")
     * @ORM\Column(type="string", length=5)
     */
    private $zip;

    /**
     * @Assert\NotBlank(message = "Veuillez renseigner un prix !")
     * @Assert\Length(max="11",
     *     maxMessage="Le prix ne peut pas dépasser 11 chiffres !")
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="fav_ads")
     */
    private $fav_users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;

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

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFavUsers(): Collection
    {
        return $this->fav_users;
    }

    public function addFavUser(User $favUser): self
    {
        if (!$this->fav_users->contains($favUser)) {
            $this->fav_users[] = $favUser;
            $favUser->addFavAd($this);
        }

        return $this;
    }

    public function removeFavUser(User $favUser): self
    {
        if ($this->fav_users->contains($favUser)) {
            $this->fav_users->removeElement($favUser);
            $favUser->removeFavAd($this);
        }

        return $this;
    }
}
