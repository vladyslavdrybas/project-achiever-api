<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AchievementList;
use App\Repository\AchievementListRepository;
use App\Security\Permissions;
use App\Transfer\AchievementListCreateJsonTransfer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/achievement/list', name: "api_achievement_list")]
class AchievementListController extends AbstractController
{
    #[Route("", name: "_create", methods: ["POST"])]
    public function create(
        AchievementListCreateJsonTransfer $createTransfer,
        AchievementListRepository $achievementListRepository
    ): JsonResponse {
        $list = new AchievementList();
        $list->setTitle($createTransfer->getTitle());
        $list->setDescription($createTransfer->getDescription());
        $list->setOwner($this->getUser());

        $achievementListRepository->add($list);
        $achievementListRepository->save();

        return $this->json($this->serializer->normalize($list));
    }

    #[Route("/{achievementList}", name: "_show", methods: ["GET"])]
    #[IsGranted(Permissions::VIEW, 'achievementList', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function show(
        AchievementList $achievementList
    ): JsonResponse {
        return $this->json($this->serializer->normalize($achievementList));
    }


}
