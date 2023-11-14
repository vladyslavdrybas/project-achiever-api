<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ShareObjectTokenRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use function array_pop;
use function bin2hex;
use function explode;
use function hash;
use function random_bytes;
use function sprintf;

#[ORM\Entity(repositoryClass: ShareObjectTokenRepository::class, readOnly: false)]
#[ORM\Table(name: "share_object_token")]
class ShareObjectToken implements EntityInterface
{
    public const QUERY_IDENTIFIER = 'st';

    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::STRING, length: 64, unique: true, nullable: false)]
    protected string $id;

    #[ORM\Column(name: "target", type: Types::STRING, length: 144, unique: false, nullable: false)]
    protected string $target;

    #[ORM\Column(name: "target_id", type: Types::STRING, length: 36, unique: false, nullable: false)]
    protected string $targetId;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name:'owner_id', referencedColumnName: 'id', nullable: false)]
    protected User $owner;

    #[ORM\Column(name: "expire_at", type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $expireAt = null;

    #[ORM\Column(name: "link", type: Types::STRING, unique: false, nullable: true)]
    protected ?string $link = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    protected bool $canView = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $canEdit = false;

    public function getLinkWithToken(): string
    {
        return sprintf(
            '%s?%s=%s',
            $this->getLink(),
            static::QUERY_IDENTIFIER,
            $this->getRawId()
        );
    }

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

    public function generateId(): string
    {
        $expireAt = time();
        if (null !== $this->getExpireAt()) {
            $expireAt = $this->getExpireAt()->getTimestamp();
        }

        $salt = $this->getTarget() . $this->getTargetId() . $this->getOwner()->getRawId() . $expireAt;

        return hash('sha256', bin2hex(random_bytes(8)) . $salt);
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getTargetId(): string
    {
        return $this->targetId;
    }

    /**
     * @param string $targetId
     */
    public function setTargetId(string $targetId): void
    {
        $this->targetId = $targetId;
    }

    /**
     * @return \App\Entity\User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param \App\Entity\User $owner
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
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

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return bool
     */
    public function isCanView(): bool
    {
        return $this->canView;
    }

    /**
     * @param bool $canView
     */
    public function setCanView(bool $canView): void
    {
        $this->canView = $canView;
    }

    /**
     * @return bool
     */
    public function isCanEdit(): bool
    {
        return $this->canEdit;
    }

    /**
     * @param bool $canEdit
     */
    public function setCanEdit(bool $canEdit): void
    {
        $this->canEdit = $canEdit;
    }
}
