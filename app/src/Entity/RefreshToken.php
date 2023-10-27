<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function bin2hex;
use function method_exists;
use function random_bytes;
use function trigger_deprecation;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class, readOnly: false)]
#[ORM\Table(name: "refresh_tokens")]
class RefreshToken extends AbstractEntity implements RefreshTokenInterface
{
    #[ORM\Column(name: "refresh_token", type: Types::STRING, length: 128, unique: true, nullable: false)]
    protected ?string $refreshToken = null;

    #[ORM\Column(name: "username", type: Types::STRING, length: 36, unique: false, nullable: false)]
    protected ?string $username = null;

    #[ORM\Column(name: "valid", type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $valid = null;

    protected string $salt = '';

    protected function setSaltByUser(UserInterface $user): void
    {
        $this->salt .= $this->getRawId();
        if (method_exists($user, 'getUserIdentifier')) {
            $this->salt .= $user->getUserIdentifier();
        }
        if (method_exists($user, 'getRawId')) {
            $this->salt .= $user->getRawId();
        }
    }

    public function isValid(): bool
    {
        return $this->valid >= new \DateTime();
    }

    public function __toString(): string
    {
        return $this->getRefreshToken();
    }

    /**
     * @param string $refreshToken
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param int $ttl
     * @return \Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface
     * @throws \Exception
     */
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

        if (method_exists($user, 'getRawId')) {
            $username = $user->getRawId();
        } else if (method_exists($user, 'getUserIdentifier')) {
            $username = $user->getUserIdentifier();
        } else {
            $username = $user->getUsername();
        }

        $model->setUsername($username);
        $model->setSaltByUser($user);
        $model->setRefreshToken($refreshToken);
        $model->setValid($valid);

        return $model;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param $refreshToken
     * @return \Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface
     * @throws \Exception
     */
    public function setRefreshToken($refreshToken = null): RefreshTokenInterface
    {
        if (null === $refreshToken || '' === $refreshToken) {
            trigger_deprecation('gesdinet/jwt-refresh-token-bundle', '1.0', 'Passing an empty token to %s() to automatically generate a token is deprecated.', __METHOD__);

            $refreshToken = hash('sha512', bin2hex(random_bytes(64)) . $this->salt);
        }

        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return \Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface
     */
    public function setUsername($username): RefreshTokenInterface
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getValid(): ?DateTimeInterface
    {
        return $this->valid;
    }

    /**
     * @param $valid
     * @return \Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface
     */
    public function setValid($valid): RefreshTokenInterface
    {
        $this->valid = $valid;

        return $this;
    }
}
