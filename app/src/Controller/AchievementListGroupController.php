<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AchievementList;
use App\Entity\UserGroup;
use App\Repository\AchievementListRepository;
use App\Security\Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/achievement/list/{achievementList}/g', name: "api_achievement_list_group")]
class AchievementListGroupController extends AbstractController
{
    #[Route("/{group}", name: "_add", methods: ["POST"])]
    #[IsGranted(Permissions::EDIT, 'achievementList', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function addAchievement(
        AchievementList $achievementList,
        UserGroup $group,
        AchievementListRepository $achievementListRepository
    ): JsonResponse {
        $achievementList->addGroup($group);
        $achievementListRepository->add($achievementList);
        $achievementListRepository->save();

        return $this->json([
            'message' => 'success',
        ]);
    }
}
