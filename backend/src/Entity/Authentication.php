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

use App\Repository\AuthenticationRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Index(columns: ["type"])]
#[ORM\Index(columns: ["credential"])]
#[ORM\Index(columns: ["token"])]
#[ORM\Index(columns: ["expired_at"])]
#[ORM\Index(columns: ["status"])]
#[ORM\Index(columns: ["created_at"])]
#[ORM\Index(columns: ["updated_at"])]
#[ORM\Table(name: "authentications", options:["engine"=> "InnoDB"])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: AuthenticationRepository::class)]
class Authentication
{
    use BaseEntity;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ["unsigned" => true])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $type = null;

     #[ORM\Column(length: 191, nullable: false)]
    private ?string $credential = null;

    #[ORM\Column(length: 191, nullable: false)]
    private ?string $token = null;

    #[ORM\Column(name: 'expired_at', type: 'datetime', nullable: true)]
    private ?DateTime $expiredAt = null;


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
     * Get the value of type
     *
     * @return ?string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param ?string $type
     *
     * @return self
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of credential
     *
     * @return ?string
     */
    public function getCredential(): ?string
    {
        return $this->credential;
    }

    /**
     * Set the value of credential
     *
     * @param ?string $credential
     *
     * @return self
     */
    public function setCredential(?string $credential): self
    {
        $this->credential = $credential;

        return $this;
    }

    /**
     * Get the value of token
     *
     * @return ?string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @param ?string $token
     *
     * @return self
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of expiredAt
     *
     * @return ?DateTime
     */
    public function getExpiredAt(): ?DateTime
    {
        return $this->expiredAt;
    }

    /**
     * Set the value of expiredAt
     *
     * @param ?DateTime $expiredAt
     *
     * @return self
     */
    public function setExpiredAt(?DateTime $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }
}
