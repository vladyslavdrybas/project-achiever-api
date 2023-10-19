<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use function str_replace;

#[ORM\Entity(repositoryClass: TagRepository::class, readOnly: false)]
#[ORM\Table(name: "tag")]
class Tag implements EntityInterface
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::STRING, length: 30, unique: true)]
    protected string $id;

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
        $this->id = $this->clear($id);
    }

    protected function clear(string $id): string
    {
        return str_replace(
            [
                '#',
                ' ',
                '"',
                '\'',
                ';',
                '\\',
                ']',
                '[',
                '}',
                '{',
                ')',
                '(',
                '*',
                '+',
                '%',
                '.',
                ',',
                '$'
            ],
            '',
            $id
        );
    }
}
