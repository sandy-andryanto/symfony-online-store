<?php

/**
* This file is part of the Sandy Andryanto Online Store Website.
*
* @author     Sandy Andryanto <sandy.andryanto.blade@gmail.com>
* @copyright  2025
*
* For the full copyright and license information,
* please view the LICENSE.md file that was distributed
* with this source code.
*/

namespace App\Entity;

use App\Repository\ProductInventoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Index(columns: ["stock"])]
#[ORM\Index(columns: ["status"])]
#[ORM\Index(columns: ["created_at"])]
#[ORM\Index(columns: ["updated_at"])]
#[ORM\Table(name: "products_inventories", options:["engine"=> "InnoDB"])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ProductInventoryRepository::class)]
class ProductInventory
{
    use BaseEntity;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ["unsigned" => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private ?Product $product = null;

     #[ORM\ManyToOne(targetEntity: Size::class)]
    #[ORM\JoinColumn(name: 'size_id', referencedColumnName: 'id', nullable: false)]
    private ?Size $size = null;

    #[ORM\ManyToOne(targetEntity: Colour::class)]
    #[ORM\JoinColumn(name: 'colour_id', referencedColumnName: 'id', nullable: false)]
    private ?Colour $colour = null;

    #[ORM\Column(options: ["unsigned" => true, "default"=> 0])]
    private int $stock = 0;

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
     * Get the value of product
     *
     * @return ?Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * Set the value of product
     *
     * @param ?Product $product
     *
     * @return self
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get the value of size
     *
     * @return ?Size
     */
    public function getSize(): ?Size
    {
        return $this->size;
    }

    /**
     * Set the value of size
     *
     * @param ?Size $size
     *
     * @return self
     */
    public function setSize(?Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the value of colour
     *
     * @return ?Colour
     */
    public function getColour(): ?Colour
    {
        return $this->colour;
    }

    /**
     * Set the value of colour
     *
     * @param ?Colour $colour
     *
     * @return self
     */
    public function setColour(?Colour $colour): self
    {
        $this->colour = $colour;

        return $this;
    }

    /**
     * Get the value of stock
     *
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * Set the value of stock
     *
     * @param int $stock
     *
     * @return self
     */
    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}
