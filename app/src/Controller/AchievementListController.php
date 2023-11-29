<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\RouteConstants;
use App\Entity\Achievement;
use App\Entity\AchievementList;
use App\Entity\EntityInterface;
use App\Entity\User;
use App\Repository\AchievementListRepository;
use App\Security\Permissions;
use App\Transfer\AchievementListCreateJsonTransfer;
use App\ValueResolver\UserValueResolver;
use Faker\Factory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
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
        "/{user}/own/{timestamp}/{offset}/{limit}/{timeRange}",
        name: "_user_own_lists",
        requirements: [
            'offset' => '\d+',
            'limit' => RouteConstants::REQUIREMENT_LIST_LIMIT,
            'timestamp' => '\d+',
            'timeRange' => 'newer|older',
        ],
        defaults: [
            'offset' => 0,
            'limit' => 5,
            'timestamp' => RouteConstants::PARAM_TIMESTAMP_DEFAULT,
            'timeRange' => 'older',
        ],
        methods: ["GET"]
    )]
    public function listOwned(
        #[ValueResolver(UserValueResolver::class)]
        User $user,
        int $timestamp,
        int $offset,
        int $limit,
        string $timeRange,
        AchievementListRepository $achievementListRepository
    ): JsonResponse {
        $lists = $achievementListRepository->findOwnedLists(
            $user,
            $timestamp,
            $offset,
            $limit,
            $timeRange === 'older' ? EntityInterface::TIME_RANGE_OLDER : EntityInterface::TIME_RANGE_NEWER
        );
        $faker = Factory::create();

        $data= [];
        foreach ($lists as $key => $list)
        {
            $normalized = $this->serializer->normalize($list, Achievement::class);

            $data[$key]['id'] = $faker->uuid();
            $data[$key]['object'] = 'Post';
            $data[$key]['type'] = 'list';
            $data[$key]['thumbnail'] = null;
            $data[$key]['owner'] = $normalized['owner'];
            $data[$key]['data'] = $normalized;
            $data[$key]['data']['achievementsAmount'] = $list->getAchievements()->count();
            $data[$key]['createdAt'] = $normalized['createdAt'];
            $data[$key]['updatedAt'] = $normalized['updatedAt'];

        }

        return $this->json($data);
    }

    #[Route(
        "/{user}/share/{timestamp}/{offset}/{limit}/{timeRange}",
        name: "_user_share_lists",
        requirements: [
            'offset' => '\d+',
            'limit' => RouteConstants::REQUIREMENT_LIST_LIMIT,
            'timestamp' => '\d+',
            'timeRange' => 'newer|older',
        ],
        defaults: [
            'offset' => 0,
            'limit' => 5,
            'timestamp' => RouteConstants::PARAM_TIMESTAMP_DEFAULT,
            'timeRange' => 'older',
        ],
        methods: ["GET"]
    )]
    public function listShare(
        #[ValueResolver(UserValueResolver::class)]
        User $user,
        int $timestamp,
        int $offset,
        int $limit,
        string $timeRange,
        AchievementListRepository $achievementListRepository
    ): JsonResponse {
        $lists = $achievementListRepository->findShareLists(
            $user,
            $timestamp,
            $offset,
            $limit,
            $timeRange === 'older' ? EntityInterface::TIME_RANGE_OLDER : EntityInterface::TIME_RANGE_NEWER
        );
        $faker = Factory::create();

        $data= [];
        foreach ($lists as $key => $list)
        {
            $normalized = $this->serializer->normalize($list, Achievement::class);

            $data[$key]['id'] = $faker->uuid();
            $data[$key]['object'] = 'Post';
            $data[$key]['type'] = 'list';
            $data[$key]['thumbnail'] = null;
            $data[$key]['owner'] = $normalized['owner'];
            $data[$key]['data'] = $normalized;
            $data[$key]['createdAt'] = $normalized['createdAt'];
            $data[$key]['updatedAt'] = $normalized['updatedAt'];
        }

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
