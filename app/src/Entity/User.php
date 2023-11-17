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
use function bin2hex;
use function hash;
use function hex2bin;
use function microtime;
use function random_bytes;
use function time;
use function uniqid;

// TODO add user password reset
// TODO add user email confirmation
// TODO add user email (unique identifier) change/update
// TODO add user subscriptions
// TODO add payments
// TODO add mail promotions for user
// TODO add web push notification promotions for user
// TODO add web/email reminders for user to do some action
// TODO FOLLOW user for public changes
#[ORM\Entity(repositoryClass: UserRepository::class, readOnly: false)]
#[ORM\Table(name: "user")]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email.')]
class User extends AbstractEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column( name: "roles", type: Types::JSON, nullable: false )]
    protected array $roles = [self::ROLE_USER];

    #[Assert\Email]
    #[Assert\NotBlank]
    #[ORM\Column( name: "email", type: Types::STRING, length: 180, unique: true, nullable: false )]
    protected string $email;

    // TODO remove nullable, make it mandatory
    #[ORM\Column( name: "username", type: Types::STRING, length: 100, unique: true, nullable: true)]
    protected string $username;

    #[ORM\Column(name: "password", type: Types::STRING, length: 100, unique: false, nullable: false)]
    protected string $password;

    #[ORM\Column( name: "is_email_verified", type: 'boolean', options: ["default" => false] )]
    protected bool $isEmailVerified = false;

    #[ORM\Column(name: "is_active", type: Types::BOOLEAN, options: ["default" => true])]
    protected bool $isActive = true;

    #[ORM\Column(name: "is_banned", type: Types::BOOLEAN, options: ["default" => false])]
    protected bool $isBanned = false;

    #[ORM\Column(name: "is_deleted", type: Types::BOOLEAN, options: ["default" => false])]
    protected bool $isDeleted = false;

    #[ORM\Column(name: "locale", type: Types::STRING, length: 5, options: ["default" => 'en'])]
    protected string $locale = 'en';

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Achievement::class)]
    protected Collection $achievements;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FirebaseCloudMessaging::class)]
    protected Collection $firebaseCloudMessagingTokens;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: UserGroup::class)]
    protected Collection $ownedUserGroups;

    #[ORM\OneToMany(mappedBy: 'member', targetEntity: UserGroupRelation::class)]
    protected Collection $userGroupRelations;

    public function __construct()
    {
        parent::__construct();
        $this->achievements = new ArrayCollection();
        $this->firebaseCloudMessagingTokens = new ArrayCollection();
        $this->ownedUserGroups = new ArrayCollection();
        $this->userGroupRelations = new ArrayCollection();
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
        return $this->username;
    }

    protected function generateRandomUsername(): string
    {
        return sprintf(
            '%s.%s'
            , uniqid('u', false)
            , bin2hex(random_bytes(3))
        );
    }

    public function setRandomUsername(): void
    {
        $this->setUsername($this->generateRandomUsername());
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
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

    public function addAchievement(Achievement $achievement): void
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements->add($achievement);
            $achievement->setOwner($this);
        }
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $achievements
     */
    public function setAchievements(ArrayCollection|Collection $achievements): void
    {
        foreach ($achievements as $achievement) {
            if (!$achievement instanceof Achievement) {
                throw new \Exception('Should be instance of Achievement');
            }

            $this->addAchievement($achievement);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFirebaseCloudMessagingTokens(): Collection
    {
        return $this->firebaseCloudMessagingTokens;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $firebaseCloudMessagingTokens
     */
    public function setFirebaseCloudMessagingTokens(Collection $firebaseCloudMessagingTokens): void
    {
        $this->firebaseCloudMessagingTokens = $firebaseCloudMessagingTokens;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\App\Entity\UserGroup[]
     */
    public function getOwnedUserGroups(): Collection
    {
        return $this->ownedUserGroups;
    }

    /**
     * @param \App\Entity\UserGroup $group
     * @return void
     */
    public function addOwnedUserGroup(UserGroup $group): void
    {
        if (!$this->ownedUserGroups->contains($group)) {
            $this->ownedUserGroups->add($group);
            $group->setOwner($this);
        }
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $ownedUserGroups
     */
    public function setOwnedUserGroups(Collection $ownedUserGroups): void
    {
        foreach ($ownedUserGroups as $group) {
            if (!$group instanceof UserGroup) {
                throw new \Exception('Group should be instance of UserGroup');
            }

            $this->addOwnedUserGroup($group);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\App\Entity\UserGroupRelation[]
     */
    public function getUserGroupRelations(): Collection
    {
        return $this->userGroupRelations;
    }

    public function addMembership(UserGroupRelation $relation): void
    {
        if (!$this->userGroupRelations->contains($relation)) {
            $this->userGroupRelations->add($relation);
        } else {
            $relation = $this->userGroupRelations->get($this->userGroupRelations->indexOf($relation));
        }

        $relation->setMember($this);
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $userGroupRelations
     */
    public function setUserGroupRelations(Collection $userGroupRelations): void
    {
        foreach ($userGroupRelations as $groupRelation) {
            if (!$groupRelation instanceof UserGroupRelation) {
                throw new \Exception('Membership should be instance of UserGroupRelation');
            }

            $this->addMembership($groupRelation);
        }
    }
}
