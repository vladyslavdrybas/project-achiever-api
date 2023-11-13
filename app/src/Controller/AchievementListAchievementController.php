<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\AchievementList;
use App\Repository\AchievementListRepository;
use App\Security\Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/achievement/list/{achievementList}/a', name: "api_achievement_list_achievement")]
class AchievementListAchievementController extends AbstractController
{
    #[Route("/{achievement}", name: "_add", methods: ["POST"])]
    #[IsGranted(Permissions::EDIT, 'achievementList', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function addAchievement(
        AchievementList $achievementList,
        Achievement $achievement,
        AchievementListRepository $achievementListRepository
    ): JsonResponse {
        $achievementList->addAchievement($achievement);
        $achievementListRepository->add($achievementList);
        $achievementListRepository->save();

        return $this->json([
            'message' => 'success',
        ]);
    }

    #[Route("/{achievement}", name: "_show", methods: ["GET"])]
    #[IsGranted(Permissions::VIEW, 'achievementList', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function showAchievement(
        AchievementList $achievementList,
        Achievement $achievement
    ): JsonResponse {
        $isAchievementInList = false;
        foreach ($achievementList->getAchievements() as $listAchievement)
        {
            if ($listAchievement === $achievement) {
                $isAchievementInList = true;
                break;
            }
        }

        if (!$isAchievementInList) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->serializer->normalize($achievement));
    }
}
