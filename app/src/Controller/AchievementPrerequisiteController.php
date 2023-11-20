<?php

declare(strict_types=1);

namespace App\Controller;

use App\Builder\AchievementBuilder;
use App\Entity\Achievement;
use App\Entity\AchievementList;
use App\Repository\AchievementPrerequisiteRelationRepository;
use App\Security\Permissions;
use App\Transfer\AchievementPrerequisiteRelationTransfer;
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
        AchievementBuilder $achievementBuilder,
        Security $security
    ): JsonResponse {
        if (!$security->isGranted(Permissions::VIEW, $transfer->getPrerequisite())) {
            throw new AccessDeniedHttpException('Access denied to prerequisite.');
        }

        if (!$security->isGranted(Permissions::EDIT, $transfer->getAchievement())) {
            throw new AccessDeniedHttpException('Permissions restricted to attach prerequisite to achievement.');
        }

        $relation = $achievementBuilder->prerequisiteRelation(
            $transfer->getAchievement(),
            $transfer->getPrerequisite(),
            $transfer->getPriority(),
            $transfer->getCondition(),
            $transfer->isRequired()
        );

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
                // TODO trying to build full tree in one request could overload server
//                $tree['prerequisites'][] = $this->buildPrerequisiteTree($relation->getPrerequisite());
                $tree['prerequisites'][] = [
                    'id' => $relation->getPrerequisite()->getRawId(),
                    'title' => $relation->getPrerequisite()->getTitle(),
                    'priority' => $relation->getPriority(),
                    'isRequired' => $relation->isRequired() ? 'yes' : 'no',
                    'lists' => $relation->getAchievement()->getLists()->map(function (AchievementList $list) {
                        return [
                            'id' => $list->getRawId(),
                            'title' => $list->getTitle(),
                        ];
                    }),
                ];
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
                // TODO trying to build full tree in one request could overload server
//                $tree['achievements'][] = $this->buildAchievementTree($relation->getAchievement());
                $tree['achievements'][] = [
                    'id' => $relation->getAchievement()->getRawId(),
                    'title' => $relation->getAchievement()->getTitle(),
                    'priority' => $relation->getPriority(),
                    'isRequired' => $relation->isRequired() ? 'yes' : 'no',
                    'lists' => $relation->getAchievement()->getLists()->map(function (AchievementList $list) {
                        return [
                            'id' => $list->getRawId(),
                            'title' => $list->getTitle(),
                        ];
                    }),
                ];
            }
        }

        return $tree;
    }
}
