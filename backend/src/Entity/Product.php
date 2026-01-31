<?php

/**
* This file is part of the Sandy Andryanto Online Store Website.
*
* @author     Sandy Andryanto <sandy.andryanto.official@gmail.com>
* @copyright  2025
*
* For the full copyright and license information,
* please view the LICENSE.md file that was distributed
* with this source code.
*/

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Index(columns: ["sku"])]
#[ORM\Index(columns: ["image"])]
#[ORM\Index(columns: ["name"])]
#[ORM\Index(columns: ["price"])]
#[ORM\Index(columns: ["total_order"])]
#[ORM\Index(columns: ["total_rating"])]
#[ORM\Index(columns: ["status"])]
#[ORM\Index(columns: ["created_at"])]
#[ORM\Index(columns: ["updated_at"])]
#[ORM\Table(name: "products", options:["engine"=> "InnoDB"])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use BaseEntity;

    #[ORM\JoinTable(name: 'products_wishlists', options: ["engine" => "InnoDB"])]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: User::class)]
    private $wishlists;

    #[ORM\JoinTable(name: 'products_categories', options: ["engine" => "InnoDB"])]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'category_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Category::class)]
    private $categories;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ["unsigned" => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Brand::class)]
    #[ORM\JoinColumn(name: 'brand_id', referencedColumnName: 'id', nullable: false)]
    private ?Brand $brand = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $image = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $sku = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(options: ["unsigned" => true, "default"=> 0], type: 'decimal', precision: 18, scale: 4)]
    private float $price = 0;

    #[ORM\Column(name: 'total_order', options: ["unsigned" => true, "default"=> 0])]
    private int $totalOrder = 0;

    #[ORM\Column(name: 'total_rating', options: ["unsigned" => true, "default"=> 0])]
    private int $totalRating = 0;

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $details = null;

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $description = null;

    #[ORM\Column(name: 'published_date', type: 'datetime', nullable: true)]
    private ?DateTime $publishedDate = null;


    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of brand
     *
     * @return ?Brand
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * Set the value of brand
     *
     * @param ?Brand $brand
     *
     * @return self
     */
    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get the value of sku
     *
     * @return ?string
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * Set the value of sku
     *
     * @param ?string $sku
     *
     * @return self
     */
    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param ?string $name
     *
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of price
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @param float $price
     *
     * @return self
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of totalOrder
     *
     * @return int
     */
    public function getTotalOrder(): int
    {
        return $this->totalOrder;
    }

    /**
     * Set the value of totalOrder
     *
     * @param int $totalOrder
     *
     * @return self
     */
    public function setTotalOrder(int $totalOrder): self
    {
        $this->totalOrder = $totalOrder;

        return $this;
    }

    /**
     * Get the value of totalRating
     *
     * @return int
     */
    public function getTotalRating(): int
    {
        return $this->totalRating;
    }

    /**
     * Set the value of totalRating
     *
     * @param int $totalRating
     *
     * @return self
     */
    public function setTotalRating(int $totalRating): self
    {
        $this->totalRating = $totalRating;

        return $this;
    }

    /**
     * Get the value of details
     *
     * @return ?string
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * Set the value of details
     *
     * @param ?string $details
     *
     * @return self
     */
    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param ?string $description
     *
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of publishedDate
     *
     * @return ?DateTime
     */
    public function getPublishedDate(): ?DateTime
    {
        return $this->publishedDate;
    }

    /**
     * Set the value of publishedDate
     *
     * @param ?DateTime $publishedDate
     *
     * @return self
     */
    public function setPublishedDate(?DateTime $publishedDate): self
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    /**
     * Get the value of wishlists
     */
    public function getWishlists()
    {
        return $this->wishlists;
    }

    /**
     * Set the value of wishlists
     */
    public function setWishlists($wishlists): self
    {
        $this->wishlists = $wishlists;

        return $this;
    }

    /**
     * Get the value of categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set the value of categories
     */
    public function setCategories($categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get the value of image
     *
     * @return ?string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @param ?string $image
     *
     * @return self
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
