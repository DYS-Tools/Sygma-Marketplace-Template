<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Blog\AppBundle\Entity\Traits\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    //new \Datetime('now')use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="product")
     */
    private $category;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(
     *     maxSize = "1024k",
     *     maxSizeMessage="Le fichier excède 1024k.",
     *     mimeTypes = {"application/zip"},
     *     mimeTypesMessage = "formats autorisés: ZIP uniquement"
     * )
     */
    private $file;

    /**
     * @ORM\Column(type="datetime")
     */
    private $published;

    /**

     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $demo_link;

    /**
     * @ORM\Column(type="integer")
     */
    private $number_sale = 0;

    /**
     * @ORM\Column(type="boolean" ,options={"default":false})
     */
    private $verified = false;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(
     * maxSize="1000k",
     * maxSizeMessage="Le fichier excède 1000Ko.",
     * mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     * mimeTypesMessage= "formats autorisés: png, jpeg, jpg"
     * )
     * @Assert\Image(
     *     allowLandscape = true,
     *     allowPortrait = false,
     *     minWidth = 1190,
     *     maxWidth = 1200,
     *     minHeight = 890,
     *     maxHeight = 900
     * )
     */
    private $img1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(
     * maxSize="1000k",
     * maxSizeMessage="Le fichier excède 1000Ko.",
     * mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     * mimeTypesMessage= "formats autorisés: png, jpeg, jpg"
     * )
     * @Assert\Image(
     *     allowLandscape = true,
     *     allowPortrait = false,
     *     minWidth = 1190,
     *     maxWidth = 1200,
     *     minHeight = 890,
     *     maxHeight = 900
     * )
     */
    private $img2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(
     * maxSize="1000k",
     * maxSizeMessage="Le fichier excède 1000Ko.",
     * mimeTypes={"image/png", "image/jpeg", "image/jpg"},
     * mimeTypesMessage= "formats autorisés: png, jpeg, jpg"
     * )
     * @Assert\Image(
     *     allowLandscape = true,
     *     allowPortrait = false,
     *     minWidth = 1190,
     *     maxWidth = 1200,
     *     minHeight = 890,
     *     maxHeight = 900
     * )
     */
    private $img3;

    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished($published): self
    {
        $this->published = $published;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDemoLink(): ?string
    {
        return $this->demo_link;
    }

    public function setDemoLink(?string $demo_link): self
    {
        $this->demo_link = $demo_link;

        return $this;
    }

    public function getNumberSale(): ?int
    {
        return $this->number_sale;
    }

    public function setNumberSale(int $number_sale): self
    {
        $this->number_sale = $number_sale;

        return $this;
    }

    public function getVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getImg1(): ?string
    {
        return $this->img1;
    }

    public function setImg1(string $img1): self
    {
        $this->img1 = $img1;

        return $this;
    }

    public function getImg2(): ?string
    {
        return $this->img2;
    }

    public function setImg2(?string $img2): self
    {
        $this->img2 = $img2;

        return $this;
    }

    public function getImg3(): ?string
    {
        return $this->img3;
    }

    public function setImg3(?string $img3): self
    {
        $this->img3 = $img3;

        return $this;
    }
}
