<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AchievementList;
use App\Entity\UserGroup;
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

    #[Route(
        "/my/owned/{offset}/{limit}",
        name: "_my_owned_lists",
        requirements: ['offset' => '\d+', 'limit' => '5|10|20|50'],
        defaults: ['offset' => 0, 'limit' => 5],
        methods: ["GET"]
    )]
    public function listOwned(
        int $offset,
        int $limit,
        AchievementListRepository $achievementListRepository
    ): JsonResponse {
        $lists = $achievementListRepository->findOwnedLists($this->getUser(), $offset, $limit);

        $data = $this->serializer->normalize($lists);

        return $this->json($data);
    }

    #[Route(
        "/my/share/{offset}/{limit}",
        name: "_list_of_share",
        requirements: ['offset' => '\d+', 'limit' => '5|10|20|50'],
        defaults: ['offset' => 0, 'limit' => 5],
        methods: ["GET"]
    )]
    public function listShare(
        int $offset,
        int $limit,
        AchievementListRepository $achievementListRepository
    ): JsonResponse {
        $lists = $achievementListRepository->findShareLists($this->getUser(), $offset, $limit);

        $data = $this->serializer->normalize($lists);

        return $this->json($data);
    }

    #[Route(
        "/{achievementList}/members/{offset}/{limit}",
        name: "_members",
        requirements: ['offset' => '\d+', 'limit' => '5|10|20|50'],
        defaults: ['offset' => 0, 'limit' => 5],
        methods: ["GET"]
    )]
    #[IsGranted(Permissions::VIEW, 'achievementList', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function members(
        AchievementList $achievementList,
        int $offset,
        int $limit,
        AchievementListRepository $achievementListRepository
    ): JsonResponse {
        $lists = $achievementListRepository->findMembers($achievementList, $offset, $limit);

        $data = $this->serializer->normalize($lists);

        return $this->json($data);
    }
}
