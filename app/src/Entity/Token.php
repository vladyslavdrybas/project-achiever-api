<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TokenRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use function array_pop;
use function explode;

#[ORM\Entity(repositoryClass: TokenRepository::class, readOnly: false)]
#[ORM\Table(name: "token")]
class Token implements EntityInterface
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::STRING, length: 128, unique: true)]
    protected string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tokens')]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected User $user;

    #[ORM\Column(
        name: "expire_at",
        type: Types::DATETIME_IMMUTABLE,
        nullable: true
    )]
    protected ?DateTimeInterface $expireAt;

    /**
     * @return string
     */
    public function getObject(): string
    {
        $namespace = explode('\\', static::class);

        return array_pop($namespace);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRawId(): string
    {
        return $this->getId();
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
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
}
