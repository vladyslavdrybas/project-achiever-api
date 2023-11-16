<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\AchievementPrerequisiteRelation;
use App\Repository\AchievementPrerequisiteRelationRepository;
use App\Security\Permissions;
use App\Transfer\AchievementPrerequisiteRelationTransfer;
use InvalidArgumentException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/achievement/prerequisite', name: "api_achievement_prerequisite")]
class AchievementPrerequisiteController extends AbstractController
{
    #[Route("", name: "_create", methods: ["POST"])]
    public function create(
        AchievementPrerequisiteRelationTransfer $transfer,
        AchievementPrerequisiteRelationRepository $repository,
        Security $security
    ): JsonResponse {
        if (!$security->isGranted(Permissions::VIEW, $transfer->getPrerequisite())) {
            throw new AccessDeniedHttpException('Access denied to prerequisite.');
        }

        if (!$security->isGranted(Permissions::EDIT, $transfer->getAchievement())) {
            throw new AccessDeniedHttpException('Permissions restricted to attach prerequisite to achievement.');
        }

        if ($transfer->getPrerequisite() === $transfer->getAchievement()) {
            throw new InvalidArgumentException('Prerequisite cannot reference on itself.');
        }

        $loop = $repository->findOneBy([
            'achievement' => $transfer->getPrerequisite(),
            'prerequisite' => $transfer->getAchievement()
        ]);

        if (null !== $loop) {
            throw new InvalidArgumentException('Attempt to creat relation loop.');
        }

        $relation = new AchievementPrerequisiteRelation();
        $relation->setAchievement($transfer->getAchievement());
        $relation->setPrerequisite($transfer->getPrerequisite());
        $relation->setPriority($transfer->getPriority());
        $relation->setCondition($transfer->getCondition());
        $relation->setIsRequired($transfer->isRequired());

        $repository->add($relation);
        $repository->save();

        return $this->success();
    }

    #[Route("/tree/prerequisites/l/{achievementList}/a/{achievement}", name: "_prerequisite_tree", methods: ["GET"])]
    #[IsGranted(Permissions::VIEW, 'achievement', 'Access Denied.', JsonResponse::HTTP_UNAUTHORIZED)]
    public function treePrerequisite(
        Achievement $achievement
    ): JsonResponse {
        $tree = $this->buildPrerequisiteTree($achievement);

        return $this->json($tree);
    }

    protected function buildPrerequisiteTree(Achievement $achievement): array
    {
        $tree = [
            'id' => $achievement->getRawId(),
            'title' => $achievement->getTitle(),
        ];

        $meAchievementIn = $achievement->getMeAchievementIn();
        if ($meAchievementIn->isEmpty()) {
            return $tree;
        }

        $tree['prerequisites' ] = [];

        foreach ($meAchievementIn as $relation) {
            if ($relation->getAchievement() === $achievement) {
                $tree['prerequisites'][] = $this->buildPrerequisiteTree($relation->getPrerequisite());
            }
        }

        return $tree;
    }

    #[Route("/tree/achievements/l/{achievementList}/a/{achievement}", name: "_achievement_tree", methods: ["GET"])]
    #[IsGranted(Permissions::VIEW, 'achievement', 'Access Denied.', JsonResponse::HTTP_UNAUTHORIZED)]
    public function treeAchievement(
        Achievement $achievement
    ): JsonResponse {
        $tree = $this->buildAchievementTree($achievement);

        return $this->json($tree);
    }

    protected function buildAchievementTree(Achievement $achievement): array
    {
        $tree = [
            'id' => $achievement->getRawId(),
            'title' => $achievement->getTitle(),
        ];

        $mePrerequisiteIn = $achievement->getMePrerequisiteIn();
        if ($mePrerequisiteIn->isEmpty()) {
            return $tree;
        }

        $tree['achievements' ] = [];

        foreach ($mePrerequisiteIn as $relation) {
            if ($relation->getPrerequisite() === $achievement) {
                $tree['achievements'][] = $this->buildAchievementTree($relation->getAchievement());
            }
        }

        return $tree;
    }
}
