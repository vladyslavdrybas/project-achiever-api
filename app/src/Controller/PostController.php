<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\RouteConstants;
use App\Entity\Achievement;
use App\Entity\EntityInterface;
use App\Entity\User;
use App\ValueResolver\UserValueResolver;
use Faker\Factory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post', name: "api_post")]
class PostController extends AbstractController
{
    #[Route("/{user}/{timestamp}/{offset}/{limit}/{timeRange}",
        name: "_user_post_collection",
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
        methods: ["GET"],
    )]
    public function profile(
        #[ValueResolver(UserValueResolver::class)]
        User $user,
        int $timestamp,
        int $offset,
        int $limit,
        string $timeRange
    ): JsonResponse {
        $achievements = $this->entityManager->getRepository(Achievement::class)->findForUserByTimestamp(
            $user,
            $timestamp,
            $offset,
            $limit,
            $timeRange === 'older' ? EntityInterface::TIME_RANGE_OLDER : EntityInterface::TIME_RANGE_NEWER
        );
        $faker = Factory::create();

        $data = [];

        foreach ($achievements as $key => $achievement)
        {
            $normalized = $this->serializer->normalize($achievement, Achievement::class);

            $data[$key]['id'] = $faker->uuid();
            $data[$key]['object'] = 'Post';
            $data[$key]['type'] = 'achievement';
            $data[$key]['thumbnail'] = null;
            $data[$key]['owner'] = $normalized['owner'];
            $data[$key]['data'] = $normalized;
            $data[$key]['createdAt'] = $normalized['createdAt'];
            $data[$key]['updatedAt'] = $normalized['updatedAt'];
        }

        return $this->json($data);
    }
}
