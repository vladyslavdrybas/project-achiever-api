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
    #[ORM\Column(name: "token", type: Types::STRING, length: 512, unique: false, nullable: false)]
    protected string $token;

    #[ORM\Column(name: "device_type", type: Types::STRING, length: 10, unique: false, nullable: false, enumType: FcmTokenDeviceType::class)]
    protected FcmTokenDeviceType $deviceType = FcmTokenDeviceType::UNKNOWN;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'firebaseCloudMessagingTokens')]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected User $user;

    #[ORM\Column(name: "is_active", type: Types::BOOLEAN, options: ["default" => true])]
    protected bool $isActive = true;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return FcmTokenDeviceType
     */
    public function getDeviceType(): FcmTokenDeviceType
    {
        return $this->deviceType;
    }

    /**
     * @param FcmTokenDeviceType|string $deviceType
     */
    public function setDeviceType(FcmTokenDeviceType|string $deviceType): void
    {
        $deviceType = FcmTokenDeviceType::tryFrom($deviceType);
        if (null === $deviceType) {
            $deviceType = FcmTokenDeviceType::UNKNOWN;
        }

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

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}
