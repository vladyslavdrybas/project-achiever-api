<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FirebaseCloudMessagingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FirebaseCloudMessagingRepository::class, readOnly: false)]
#[ORM\Table(name: "firebase_cloud_messaging")]
class FirebaseCloudMessaging extends AbstractEntity
{
    #[ORM\Column(name: "token", type: Types::STRING, length: 512, unique: false, nullable: true)]
    protected ?string $token = null;

    #[ORM\Column(name: "device_type", type: Types::STRING, length: 512, unique: false, nullable: false)]
    protected string $deviceType;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'firebaseCloudMessagingTokens')]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected User $user;

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getDeviceType(): string
    {
        return $this->deviceType;
    }

    /**
     * @param string $deviceType
     */
    public function setDeviceType(string $deviceType): void
    {
        $this->deviceType = $deviceType;
    }

    /**
     * @return \App\Entity\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param \App\Entity\User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
