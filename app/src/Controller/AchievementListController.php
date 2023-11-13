<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AchievementList;
use App\Entity\User;
use App\Repository\AchievementListRepository;
use App\Repository\AchievementRepository;
use App\Transfer\AchievementListCreateJsonTransfer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use function array_slice;

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
    public function show(
        AchievementList $achievementList
    ): JsonResponse {
        return $this->json($this->serializer->normalize($achievementList));
    }

    #[Route(
        "/{user}/{offset}/{length}",
        name: "_list_of_user",
        requirements: ['offset' => '\d+', 'length' => '5|10|20|50'],
        defaults: ['offset' => 0, 'length' => 5],
        methods: ["GET"]
    )]
    public function list(
        User $user,
        int $offset,
        int $length,
        AchievementRepository $achievementRepository
    ): JsonResponse {
        $achievements = $achievementRepository->findBy(
            [
                'owner' => $user,
            ],
            [
                'doneAt' => 'DESC',
                'createdAt' => 'DESC'
            ]
        );

        $achievements = array_slice($achievements, $offset, $length);

        $data = $this->serializer->normalize($achievements);

        return $this->json($data);
    }
}
