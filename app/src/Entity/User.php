<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use function array_unique;

#[ORM\Entity(repositoryClass: UserRepository::class, readOnly: false)]
#[ORM\Table(name: "user")]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email.')]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(
        name: "roles",
        type: Types::JSON,
        nullable: false
    )]
    protected array $roles = [self::ROLE_USER];

    #[Assert\Email]
    #[Assert\NotBlank]
    #[ORM\Column(
        name: "email",
        type: Types::STRING,
        length: 180,
        unique: true,
        nullable: false
    )]
    protected string $email;

    #[ORM\Column(name: "password", type: Types::STRING, length: 100, unique: false, nullable: false)]
    protected string $password;

    #[ORM\Column(
        name: "is_email_verified",
        type: 'boolean',
        options: ["default" => false]
    )]
    protected bool $isEmailVerified = false;

    #[ORM\Column(name: "is_active", type: Types::BOOLEAN, options: ["default" => true])]
    protected bool $isActive = true;

    #[ORM\Column(name: "is_banned", type: Types::BOOLEAN, options: ["default" => false])]
    protected bool $isBanned = false;

    #[ORM\Column(name: "is_deleted", type: Types::BOOLEAN, options: ["default" => false])]
    protected bool $isDeleted = false;

    #[ORM\Column(name: "locale", type: Types::STRING, length: 5, options: ["default" => 'en'])]
    protected string $locale = 'en';

    #[ORM\OneToMany(mappedBy: 'user',targetEntity: Achievement::class)]
    protected Collection $achievements;

    public function __construct()
    {
        parent::__construct();
        $this->achievements = new ArrayCollection();
    }

    public function isEqualTo(SecurityUserInterface $user): bool
    {
        return $user->getUserIdentifier() === $this->getUserIdentifier();
    }

    public function addRole(string $role): void
    {
        $this->roles[] = $role;
        $this->setRoles($this->roles);
    }

    protected function setRoles(array $roles): void
    {
        $this->roles = array_unique($roles);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function setUserIdentifier(string $identifier): void
    {
        $this->setEmail($identifier);
    }

    public function eraseCredentials(): void
    {
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return $this->isEmailVerified;
    }

    /**
     * @param bool $isEmailVerified
     */
    public function setIsEmailVerified(bool $isEmailVerified): void
    {
        $this->isEmailVerified = $isEmailVerified;
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

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->isBanned;
    }

    /**
     * @param bool $isBanned
     */
    public function setIsBanned(bool $isBanned): void
    {
        $this->isBanned = $isBanned;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getAchievements(): ArrayCollection|Collection
    {
        return $this->achievements;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $achievements
     */
    public function setAchievements(ArrayCollection|Collection $achievements): void
    {
        $this->achievements = $achievements;
    }
}
