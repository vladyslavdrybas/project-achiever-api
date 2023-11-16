<?php

namespace DataFixtures;

use App\Builder\AchievementListBuilder;
use App\Builder\UserBuilder;
use App\Builder\UserGroupBuilder;
use App\Entity\EntityInterface;
use App\Entity\UserGroupRelationType;
use App\Security\UserGroupManager;
use App\Security\UserGroupSecurityManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function filter_var;
use function rand;
use function sleep;
use function sprintf;
use function ucfirst;
use const FILTER_SANITIZE_NUMBER_INT;

class AppFixtures extends Fixture
{
    protected const POOL_KEY_USER = 'user';
    protected const POOL_KEY_USER_GROUP = 'userGroup';
    protected const POOL_KEY_ACHIEVEMENT_LIST = 'achievementList';

    protected const AMOUNT = [
        self::POOL_KEY_USER => 2,
        self::POOL_KEY_USER_GROUP => 3,
        self::POOL_KEY_ACHIEVEMENT_LIST => 13,
    ];

    protected ArrayCollection $pool;
    protected Generator $faker;
    protected BufferedOutput $output;
    protected ObjectManager $manager;
    protected SymfonyStyle $io;


    public function __construct(
        protected readonly UserBuilder $userBuilder,
        protected readonly UserGroupBuilder $userGroupBuilder,
        protected readonly UserGroupManager $groupManager,
        protected readonly UserGroupSecurityManager $userGroupSecurityManager,
        protected readonly AchievementListBuilder $achievementListBuilder
    ) {
        $this->faker = Factory::create();
        $this->output = new BufferedOutput();
        $this->io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput(
            OutputInterface::VERBOSITY_NORMAL,
            true
        ));

        $this->pool = new ArrayCollection();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->io->title('Start loading project entities:');

        $this->loadFixturesWithProgress(
            [$this, 'fixtureUser'],
            static::AMOUNT[static::POOL_KEY_USER]
        );

        $this->loadFixturesWithProgress(
            [$this, 'fixtureUserGroup'],
            static::AMOUNT[static::POOL_KEY_USER_GROUP]
        );

        $this->loadFixturesWithProgress(
            [$this, 'fixtureAddUserGroupMember'],
            (int)(static::AMOUNT[static::POOL_KEY_USER] * static::AMOUNT[static::POOL_KEY_USER_GROUP] / 1.3)
        );

        $this->loadFixturesWithProgress(
            [$this, 'fixtureAchievementList'],
            static::AMOUNT[static::POOL_KEY_ACHIEVEMENT_LIST]
        );

        $this->loadFixturesWithProgress(
            [$this, 'fixtureAddAchievementListToUserGroup'],
            static::AMOUNT[static::POOL_KEY_ACHIEVEMENT_LIST]
        );
    }

    protected function loadFixturesWithProgress(callable $fixtureGeneratorFunction, int $entitiesAmount = 1): void
    {
        $this->io->section('Loading... ' . ucfirst($fixtureGeneratorFunction[1]));
        $this->io->progressStart($entitiesAmount);

        $addCounter = 0;
        for($i = 1; $i <= $entitiesAmount; $i++) {
            call_user_func($fixtureGeneratorFunction);

            $addCounter++;
            if ($i % 15 === 0) {
                $this->manager->flush();
                $addCounter = 0;
                sleep(1);
            }

            if ($i % 5 === 0) {
                $this->io->progressAdvance(5);
            }
        }

        if ($addCounter > 0) {
            $this->manager->flush();
        }

        $this->io->progressAdvance();
        $this->io->progressFinish();
    }

    protected function addPoolEntity(string $poolKey, EntityInterface $entity): void
    {
        if (!$this->pool->containsKey($poolKey)) {
            $this->pool->set($poolKey, new ArrayCollection());
        }

        $this->pool->get($poolKey)->set($entity->getRawId(), $entity);
    }

    protected function getPoolOf(string $poolKey): ArrayCollection
    {
        if (!$this->pool->containsKey($poolKey)) {
            $this->pool->set($poolKey, new ArrayCollection());
        }

        return $this->pool->get($poolKey);
    }

    protected function getPoolRandomEntityOf(string $poolKey): EntityInterface
    {
        $pool = $this->getPoolOf($poolKey);
        if ($pool->isEmpty()) {
            throw new \Exception('Empty pool of ' . $poolKey);
        }

        $keys = $pool->getKeys();
        $elemKey = $keys[rand(0, count($keys) - 1)];

        return $pool->get($elemKey);
    }

    protected function isPublic(EntityInterface $entity): bool
    {
        return (int)filter_var($entity->getRawId(), FILTER_SANITIZE_NUMBER_INT) % 2 === 0;
    }

    public function fixtureUser(): void
    {
        $user = $this->userBuilder->baseUser($this->faker->email(), 'password');
        $this->manager->persist($user);
        $this->addPoolEntity(static::POOL_KEY_USER, $user);
    }

    public function fixtureUserGroup(): void
    {
        /** @var \App\Entity\User $owner */
        $owner = $this->getPoolRandomEntityOf(static::POOL_KEY_USER);

        $userGroup = $this->userGroupBuilder->baseUserGroup(
            $this->faker->realTextBetween(10, 125),
            $this->faker->realTextBetween(30, 125),
            $owner
        );

        $this->manager->persist($userGroup);
        $this->addPoolEntity(static::POOL_KEY_USER_GROUP, $userGroup);
    }

    public function fixtureAddUserGroupMember(): void
    {
        $roles = UserGroupRelationType::cases();
        $role = $roles[rand(0, count($roles) - 1)];

        $userGroups = $this->getPoolOf(static::POOL_KEY_USER_GROUP);
        $users = $this->getPoolOf(static::POOL_KEY_USER);

        /** @var \App\Entity\UserGroup|false $userGroup */
        $userGroup = $userGroups->current();

        if (false === $userGroup) {
            return;
        }

        if ($userGroup->getUserGroupRelations()->count() >= (int)($users->count() / 1.4)
            || $userGroup->getUserGroupRelations()->count() > rand(20, 100)
        ) {
//            $this->io->text(
//                sprintf(
//                    'Filled group %s. Go to the next.'
//                    , $userGroup->getRawId()
//                )
//            );

            $userGroups->next();
            return;
        }

        /** @var \App\Entity\User|false $member */
        $member = $users->current();
        if (false === $member) {
//            $this->io->text('Filled members. Start from the beginning.');
            /** @var \App\Entity\User $member */
            $member = $users->first();
        }

//        $this->io->newLine();
//        $this->io->text(
//            sprintf(
//                'Attempt for group %s add member %s.'
//                , $userGroup->getRawId()
//                , $member->getRawId()
//            )
//        );

        $users->next();

        $owner = $userGroup->getOwner();

        if ($member === $owner) {
//            $this->io->text('Group owner found. Do not add.');
            return;
        }

        try {
            $this->groupManager->addMember($userGroup, $member, $owner, $role->value);
        } catch (Exception $e) {
            $this->io->warning(
                sprintf(
                    '%s Group: %s; Member: %s'
                    , $e->getMessage()
                    , $userGroup->getRawId()
                    , $member->getRawId()
                )
            );
        }
    }

    public function fixtureAchievementList(): void
    {
        $users = $this->getPoolOf(static::POOL_KEY_USER);
        /** @var \App\Entity\User|bool $owner */
        $owner = $users->current();
        if (!$owner) {
            $owner = $users->first();
        }

        $list = $this->achievementListBuilder->baseAchievementList(
            $this->faker->realTextBetween(13, 125),
            $this->faker->realTextBetween(30, 255),
            $owner
        );

        $list->setIsPublic($this->isPublic($list));

        $this->manager->persist($list);
        $this->addPoolEntity(static::POOL_KEY_ACHIEVEMENT_LIST, $list);

        $users->next();
    }

    public function fixtureAddAchievementListToUserGroup(): void
    {
        $lists = $this->getPoolOf(self::POOL_KEY_ACHIEVEMENT_LIST);
        /** @var \App\Entity\AchievementList|bool $list */
        $list = $lists->current();
        if (!$list) {
            return;
        }

        $lists->next();
        if ($list->isPublic()) {
            return;
        }

        $userGroups = $this->getPoolOf(self::POOL_KEY_USER_GROUP);
        /** @var \App\Entity\UserGroup|bool $userGroup */
        $userGroup = $userGroups->current();
        if (!$userGroup) {
            $userGroup = $userGroups->first();
        }

        $owner = $list->getOwner();
        do {
            $groupCanEdit = $this->userGroupSecurityManager->canEdit($userGroup, $owner);
            if (!$groupCanEdit) {
                $userGroup = $userGroups->next();
            }

            if (!$userGroup) {
                return;
            }
        } while (!$groupCanEdit);

        $list->addGroup($userGroup);
        $this->manager->persist($list);
    }
}
