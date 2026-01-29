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

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Index(columns: ["invoice_number"])]
#[ORM\Index(columns: ["total_item"])]
#[ORM\Index(columns: ["subtotal"])]
#[ORM\Index(columns: ["total_discount"])]
#[ORM\Index(columns: ["total_taxes"])]
#[ORM\Index(columns: ["total_shipment"])]
#[ORM\Index(columns: ["total_paid"])]
#[ORM\Index(columns: ["status"])]
#[ORM\Index(columns: ["created_at"])]
#[ORM\Index(columns: ["updated_at"])]
#[ORM\Table(name: "orders", options:["engine"=> "InnoDB"])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order
{
    #[ORM\JoinTable(name: 'orders_carts', options: ["engine" => "InnoDB"])]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'product_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Product::class)]
    private $carts;

    use BaseEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ["unsigned" => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Payment::class)]
    #[ORM\JoinColumn(name: 'payment_id', referencedColumnName: 'id', nullable: true)]
    private ?Payment $payment = null;

    #[ORM\Column(name: 'invoice_number',length: 255, nullable: true)]
    private ?string $invoiceNumber = null;

    #[ORM\Column(name: 'total_item', type: 'integer', options: ["unsigned" => true, "default"=> 0])]
    private int $totalItem = 0;

    #[ORM\Column(options: ["unsigned" => true, "default"=> 0], type: 'decimal', precision: 18, scale: 4)]
    private float $subtotal = 0;

    #[ORM\Column(name: 'total_discount',options: ["unsigned" => true, "default"=> 0], type: 'decimal', precision: 18, scale: 4)]
    private float $totalDiscount = 0;

    #[ORM\Column(name: 'total_taxes',options: ["unsigned" => true, "default"=> 0], type: 'decimal', precision: 18, scale: 4)]
    private float $totalTaxes = 0;

    #[ORM\Column(name: 'total_shipment',options: ["unsigned" => true, "default"=> 0], type: 'decimal', precision: 18, scale: 4)]
    private float $totalShipment = 0;

    #[ORM\Column(name: 'total_paid',options: ["unsigned" => true, "default"=> 0], type: 'decimal', precision: 18, scale: 4)]
    private float $totalPaid = 0;

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
     * Get the value of user
     *
     * @return ?User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @param ?User $user
     *
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of payment
     *
     * @return ?Payment
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * Set the value of payment
     *
     * @param ?Payment $payment
     *
     * @return self
     */
    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get the value of invoiceNumber
     *
     * @return ?string
     */
    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    /**
     * Set the value of invoiceNumber
     *
     * @param ?string $invoiceNumber
     *
     * @return self
     */
    public function setInvoiceNumber(?string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Get the value of totalItem
     *
     * @return int
     */
    public function getTotalItem(): int
    {
        return $this->totalItem;
    }

    /**
     * Set the value of totalItem
     *
     * @param int $totalItem
     *
     * @return self
     */
    public function setTotalItem(int $totalItem): self
    {
        $this->totalItem = $totalItem;

        return $this;
    }

    /**
     * Get the value of subtotal
     *
     * @return float
     */
    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    /**
     * Set the value of subtotal
     *
     * @param float $subtotal
     *
     * @return self
     */
    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get the value of totalDiscount
     *
     * @return float
     */
    public function getTotalDiscount(): float
    {
        return $this->totalDiscount;
    }

    /**
     * Set the value of totalDiscount
     *
     * @param float $totalDiscount
     *
     * @return self
     */
    public function setTotalDiscount(float $totalDiscount): self
    {
        $this->totalDiscount = $totalDiscount;

        return $this;
    }

    /**
     * Get the value of totalTaxes
     *
     * @return float
     */
    public function getTotalTaxes(): float
    {
        return $this->totalTaxes;
    }

    /**
     * Set the value of totalTaxes
     *
     * @param float $totalTaxes
     *
     * @return self
     */
    public function setTotalTaxes(float $totalTaxes): self
    {
        $this->totalTaxes = $totalTaxes;

        return $this;
    }

    /**
     * Get the value of totalShipment
     *
     * @return float
     */
    public function getTotalShipment(): float
    {
        return $this->totalShipment;
    }

    /**
     * Set the value of totalShipment
     *
     * @param float $totalShipment
     *
     * @return self
     */
    public function setTotalShipment(float $totalShipment): self
    {
        $this->totalShipment = $totalShipment;

        return $this;
    }

    /**
     * Get the value of totalPaid
     *
     * @return float
     */
    public function getTotalPaid(): float
    {
        return $this->totalPaid;
    }

    /**
     * Set the value of totalPaid
     *
     * @param float $totalPaid
     *
     * @return self
     */
    public function setTotalPaid(float $totalPaid): self
    {
        $this->totalPaid = $totalPaid;

        return $this;
    }

    /**
     * Get the value of carts
     */
    public function getCarts()
    {
        return $this->carts;
    }

    /**
     * Set the value of carts
     */
    public function setCarts($carts): self
    {
        $this->carts = $carts;

        return $this;
    }
}
