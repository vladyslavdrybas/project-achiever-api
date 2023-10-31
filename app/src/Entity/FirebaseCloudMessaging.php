<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FirebaseCloudMessagingRepository;
use DateTimeImmutable;
use DateTimeInterface;
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

    #[ORM\Column(name: "expire_at", type: Types::DATETIME_IMMUTABLE, nullable: true )]
    protected ?DateTimeInterface $expireAt;

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
        $this->deviceType = FcmTokenDeviceType::getOrDefault($deviceType);
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
     * @return \DateTimeInterface|null
     */
    public function getExpireAt(): ?DateTimeInterface
    {
        return $this->expireAt;
    }

    /**
     * @param \DateTimeInterface|null $expireAt
     */
    public function setExpireAt(?DateTimeInterface $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    public function prolong(): void
    {
        $this->setExpireAt((new DateTimeImmutable('+9000 hours')));
    }
}
