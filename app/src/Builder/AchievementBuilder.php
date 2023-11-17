<?php

declare(strict_types=1);

namespace App\Builder;

use App\Entity\Achievement;
use App\Entity\AchievementList;
use App\Entity\AchievementPrerequisiteRelation;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\AchievementPrerequisiteRelationRepository;
use App\Repository\TagRepository;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use function array_map;

class AchievementBuilder
{
    public function __construct(
        protected readonly TagRepository $tagRepository,
        protected readonly AchievementPrerequisiteRelationRepository $achievementPrerequisiteRelationRepository
    ) {}

    public function baseAchievement(
        string $title,
        string $description,
        User $owner,
        AchievementList $achievementList,
        array $tags = [],
        ?DateTimeInterface $doneAt = null,
        bool $isPublic = false
    ): Achievement {
        if (count($tags) === 0) {
            throw new \Exception('You should add at least one tag to achievement.');
        }

        $achievement = new Achievement();

        $achievement->setTitle($title);
        $achievement->setDescription($description);
        $achievement->setOwner($owner);
        $achievement->setIsPublic($isPublic);
        $achievement->addList($achievementList);

        $doneAt = $doneAt
            ?->setTimezone(
                new DateTimeZone('UTC')
            );
        $achievement->setDoneAt($doneAt);

        $tagRepository = $this->tagRepository;
        $tags = array_map(function (string $tagId) use ($tagRepository) {
            $tag = new Tag();
            $tag->setId($tagId);

            $tagExist = $tagRepository->find($tag->getRawId());
            if (!$tagExist instanceof Tag) {
                $tagRepository->add($tag);
                $tagRepository->save();

                return $tag;
            }

            return $tagExist;
        }, $tags);

        foreach ($tags as $tag) {
            $achievement->addTag($tag);
        }

        $owner->addAchievement($achievement);

        return $achievement;
    }

    public function prerequisiteRelation(
        Achievement $achievement,
        Achievement $prerequisite,
        int $priority = 0,
        string $condition = 'complete',
        bool $isRequired = false
    ): AchievementPrerequisiteRelation {
        if ($achievement === $prerequisite) {
            throw new InvalidArgumentException('Prerequisite cannot reference on itself.');
        }

        $loop = $this->achievementPrerequisiteRelationRepository->findOneBy([
            'achievement' => $prerequisite,
            'prerequisite' => $achievement
        ]);

        if (null !== $loop) {
            throw new InvalidArgumentException('Attempt to creat relation loop.');
        }

        $relation = new AchievementPrerequisiteRelation();
        $relation->setAchievement($achievement);
        $relation->setPrerequisite($prerequisite);
        $relation->setPriority($priority);
        $relation->setCondition($condition);
        $relation->setIsRequired($isRequired);

        return $relation;
    }
}
