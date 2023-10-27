<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;
use function array_pop;
use function explode;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class, readOnly: false)]
#[ORM\Table(name: "refresh_tokens")]
class RefreshToken extends BaseRefreshToken implements EntityInterface
{
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
    public function getRawId(): string
    {
        return (string) $this->getId();
    }
}
