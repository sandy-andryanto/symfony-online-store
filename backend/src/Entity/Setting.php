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

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Index(columns: ["key_name"])]
#[ORM\Index(columns: ["status"])]
#[ORM\Index(columns: ["created_at"])]
#[ORM\Index(columns: ["updated_at"])]
#[ORM\Table(name: "settings", options:["engine"=> "InnoDB"])]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    use BaseEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ["unsigned" => true])]
    private int $id;

    #[ORM\Column(name: 'key_name', length: 255, nullable: false)]
    private ?string $keyName = null;

    #[ORM\Column(name: 'key_value', type: 'text', length: 65535, nullable: true)]
    private ?string $keyValue;

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
     * Get the value of keyName
     *
     * @return ?string
     */
    public function getKeyName(): ?string
    {
        return $this->keyName;
    }

    /**
     * Set the value of keyName
     *
     * @param ?string $keyName
     *
     * @return self
     */
    public function setKeyName(?string $keyName): self
    {
        $this->keyName = $keyName;

        return $this;
    }

    /**
     * Get the value of keyValue
     *
     * @return ?string
     */
    public function getKeyValue(): ?string
    {
        return $this->keyValue;
    }

    /**
     * Set the value of keyValue
     *
     * @param ?string $keyValue
     *
     * @return self
     */
    public function setKeyValue(?string $keyValue): self
    {
        $this->keyValue = $keyValue;

        return $this;
    }
}
