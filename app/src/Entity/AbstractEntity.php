<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\UuidV4;

use function array_pop;
use function explode;

abstract class AbstractEntity implements EntityInterface
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(name: "id", type: "uuid", unique: true)]
    protected UuidV4 $id;

    public function __construct()
    {
        $this->id = new UuidV4();
        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id->toRfc4122();
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
     * @return \Symfony\Component\Uid\UuidV4
     */
    public function getId(): UuidV4
    {
        return $this->id;
    }

    /**
     * @param \Symfony\Component\Uid\UuidV4 $id
     */
    public function setId(UuidV4 $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getRawId(): string
    {
        return $this->id->toRfc4122();
    }
}
