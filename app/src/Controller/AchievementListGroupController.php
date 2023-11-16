<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AchievementList;
use App\Entity\UserGroup;
use App\Repository\AchievementListRepository;
use App\Security\Permissions;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// TODO leave group by the user initiative
// TODO add user only after invitation confirmed
// TODO ask for access to the group
#[Route('/achievement/list/{achievementList}/g', name: "api_achievement_list_group")]
class AchievementListGroupController extends AbstractController
{
    #[Route("/{group}", name: "_add", methods: ["POST"])]
    #[IsGranted(Permissions::EDIT, 'achievementList', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function addAchievement(
        AchievementList $achievementList,
        UserGroup $group,
        AchievementListRepository $achievementListRepository,
        Security $security
    ): JsonResponse {
        if (!$security->isGranted(Permissions::EDIT, $group)) {
            throw new AccessDeniedHttpException('Access denied. Group Edit.');
        }

        $achievementList->addGroup($group);
        $achievementListRepository->add($achievementList);
        $achievementListRepository->save();

        return $this->json([
            'message' => 'success',
        ]);
    }

    #[Route("", name: "_groups_show", methods: ["GET"])]
    #[IsGranted(Permissions::VIEW, 'achievementList', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function showMembers(
        AchievementList $achievementList
    ): JsonResponse {
        $data = [];
        foreach ($achievementList->getListGroupRelations() as $relation)
        {
            /** @var UserGroup $relation */
            $data[] = [
                'id' => $relation->getRawId(),
                'title' => $relation->getTitle(),
                'owner' => [
                    'id' => $relation->getOwner()->getRawId(),
                ],
                'membersAmount' => $relation->getUserGroupRelations()->count(),
                'listsAmount' => $relation->getLists()->count(),
            ];
        }

        return $this->json($data);
    }
}
