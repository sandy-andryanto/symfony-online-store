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

use App\Repository\OrderDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Index(columns: ["price"])]
#[ORM\Index(columns: ["qty"])]
#[ORM\Index(columns: ["total"])]
#[ORM\Index(columns: ["status"])]
#[ORM\Index(columns: ["created_at"])]
#[ORM\Index(columns: ["updated_at"])]
#[ORM\Table(name: "orders_details", options:["engine"=> "InnoDB"])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: OrderDetailRepository::class)]
class OrderDetail
{
    use BaseEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ["unsigned" => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: ProductInventory::class)]
    #[ORM\JoinColumn(name: 'inventory_id', referencedColumnName: 'id', nullable: false)]
    private ?ProductInventory $inventory = null;

    #[ORM\Column(options: ["unsigned" => true, "default"=> 0], type: 'decimal', precision: 18, scale: 4)]
    private float $price = 0;

    #[ORM\Column(type: 'integer', options: ["unsigned" => true, "default"=> 0])]
    private int $qty = 0;

    #[ORM\Column(options: ["unsigned" => true, "default"=> 0], type: 'decimal', precision: 18, scale: 4)]
    private float $total = 0;

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
     * Get the value of order
     *
     * @return ?Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * Set the value of order
     *
     * @param ?Order $order
     *
     * @return self
     */
    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get the value of inventory
     *
     * @return ?ProductInventory
     */
    public function getInventory(): ?ProductInventory
    {
        return $this->inventory;
    }

    /**
     * Set the value of inventory
     *
     * @param ?ProductInventory $inventory
     *
     * @return self
     */
    public function setInventory(?ProductInventory $inventory): self
    {
        $this->inventory = $inventory;

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
     * Get the value of qty
     *
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * Set the value of qty
     *
     * @param int $qty
     *
     * @return self
     */
    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get the value of total
     *
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * Set the value of total
     *
     * @param float $total
     *
     * @return self
     */
    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }
}
