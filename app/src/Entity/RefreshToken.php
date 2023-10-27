<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function array_pop;
use function bin2hex;
use function explode;
use function method_exists;
use function random_bytes;
use function trigger_deprecation;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class, readOnly: false)]
#[ORM\Table(name: "refresh_tokens")]
class RefreshToken extends BaseRefreshToken implements EntityInterface
{
    protected string $salt = '';

    protected function setSaltByUser(UserInterface $user): void
    {
        if (method_exists($user, 'getUserIdentifier')) {
            $this->salt .= $user->getUserIdentifier();
        }
        if (method_exists($user, 'getRawId')) {
            $this->salt .= $user->getRawId();
        }
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
    public function getRawId(): string
    {
        return (string) $this->getId();
    }

    public static function createForUserWithTtl(string $refreshToken, UserInterface $user, int $ttl): RefreshTokenInterface
    {
        $valid = new \DateTime();

        // Explicitly check for a negative number based on a behavior change in PHP 8.2, see https://github.com/php/php-src/issues/9950
        if ($ttl > 0) {
            $valid->modify('+'.$ttl.' seconds');
        } elseif ($ttl < 0) {
            $valid->modify($ttl.' seconds');
        }

        $model = new static();
        $model->setUsername(method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername());
        $model->setSaltByUser($user);
        $model->setRefreshToken($refreshToken);
        $model->setValid($valid);

        return $model;
    }

    public function setRefreshToken($refreshToken = null)
    {
        if (null === $refreshToken || '' === $refreshToken) {
            trigger_deprecation('gesdinet/jwt-refresh-token-bundle', '1.0', 'Passing an empty token to %s() to automatically generate a token is deprecated.', __METHOD__);

            $refreshToken = hash('sha512', bin2hex(random_bytes(64)) . $this->salt);
        }

        $this->refreshToken = $refreshToken;

        return $this;
    }
}
