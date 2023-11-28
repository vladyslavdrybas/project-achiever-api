<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\User;
use App\ValueResolver\UserValueResolver;
use Faker\Factory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post', name: "api_post")]
class PostController extends AbstractController
{
    #[Route("/{user}/{offset}/{limit}",
        name: "_user_post_collection",
        requirements: ['offset' => '\d+', 'limit' => '1|2|3|4|5|7|11|13|17|10|20|50'],
        defaults: ['offset' => 0, 'limit' => 5],
        methods: ["GET"],
    )]
    public function profile(
        #[ValueResolver(UserValueResolver::class)]
        User $user,
        int $offset,
        int $limit
    ): JsonResponse {
        $achievements = $this->entityManager->getRepository(Achievement::class)->findAll(['createdAt', 'DESC'], $offset, $limit);
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

//        $data = [
//            [
//                'id' => 'aeff5afa-3a43-43cd-ae67-5eabe6d55200',
//                'object' => 'Post',
//                'type' => 'info',
//                "owner" => [
//                    "id" => "72344675-4a2c-4c23-a380-f6ef4a94a798",
//                    "username" => "u6558e7b3407ac.27699a",
//                    "firstname" => "vladyslav",
//                    "lastname" => "drybas",
//                    "avatar" => null,
//                    "isActive" => true
//                ],
//                "thumbnail" => null,
//                'data' =>  [
//                    "object" => "Achievement",
//                    "id" => "aeff5afa-3a43-43cd-ae67-5eabe6d55265",
//                    "tags" => [
//                        "Davonbury",
//                        "jewellkirlin"
//                    ],
//                    "thumbnail" => null,
//                    "title" => "The moment Alice appeared, she was as much as she spoke. Alice did not like to be a grin, and she felt certain it must be.",
//                    "description" => "Beautiful, beautiful Soup!' CHAPTER XI. Who Stole the Tarts? The King laid his hand upon her knee, and looking at Alice for protection. 'You shan't be beheaded!' 'What for?' said Alice. 'Why, you don't know where Dinn may be,' said the Hatter, 'when the.",
//                    "doneAt" => "2023-11-18T13:46:07+00:00",
//                    "owner" => [
//                        "id" => "72344675-4a2c-4c23-a380-f6ef4a94a798",
//                        "username" => "u6558e7b3407ac.27699a",
//                        "firstname" => "vladyslav",
//                        "lastname" => "drybas",
//                        "avatar" => null,
//                        "isActive" => true
//                    ],
//                    "contentImageLink" => null,
//                    "lists" => [
//                        [
//                            "id" => "0c3edbb1-2a20-46ed-9557-6a9dee727eb7",
//                            "title" => "And then, turning to the Gryphon. 'Well, I hardly know--No more, thank ye; I'm better now--but I'm a deal too flustered to."
//                        ],
//                        [
//                            "id" => "ef62d70f-d834-4d01-bfd7-c90d3b1bd59f",
//                            "title" => "Little Bill It was opened by another footman in livery, with a smile. There was exactly three inches high). 'But I'm not."
//                        ]
//                    ],
//                    "createdAt" => "2023-11-18T16:35:07+00:00",
//                    "updatedAt" => "2023-11-18T16:35:07+00:00",
//                    "isPublic" => true
//                ],
//            ],
//            [
//                'id' => 'aeff5afa-3a43-43cd-ae67-5eabe6d55201',
//                'object' => 'Post',
//                'type' => 'info',
//                "owner" => [
//                    "id" => "c26b9753-30bf-4b42-bd63-1e4fd23b6aa6",
//                    "username" => "u6558e7af661a8.11c6f6",
//                    "firstname" => null,
//                    "lastname" => null,
//                    "avatar" => null,
//                    "isActive" => true
//                ],
//                "thumbnail" => null,
//                'data' =>  [
//                    "object" => "Achievement",
//                    "id" => "aeff5afa-3a43-43cd-ae67-5eabe6d55265",
//                    "tags" => [
//                        "Davonbury",
//                        "jewellkirlin"
//                    ],
//                    "thumbnail" => null,
//                    "title" => "The moment Alice appeared, she was as much as she spoke. Alice did not like to be a grin, and she felt certain it must be.",
//                    "description" => "Beautiful, beautiful Soup!' CHAPTER XI. Who Stole the Tarts? The King laid his hand upon her knee, and looking at Alice for protection. 'You shan't be beheaded!' 'What for?' said Alice. 'Why, you don't know where Dinn may be,' said the Hatter, 'when the.",
//                    "doneAt" => "2023-11-18T13:46:07+00:00",
//                    "owner" => [
//                        "id" => "c26b9753-30bf-4b42-bd63-1e4fd23b6aa6",
//                        "username" => "u6558e7af661a8.11c6f6",
//                        "firstname" => null,
//                        "lastname" => null,
//                        "avatar" => null,
//                        "isActive" => true
//                    ],
//                    "contentImageLink" => null,
//                    "lists" => [
//                        [
//                            "id" => "0c3edbb1-2a20-46ed-9557-6a9dee727eb7",
//                            "title" => "And then, turning to the Gryphon. 'Well, I hardly know--No more, thank ye; I'm better now--but I'm a deal too flustered to."
//                        ],
//                        [
//                            "id" => "ef62d70f-d834-4d01-bfd7-c90d3b1bd59f",
//                            "title" => "Little Bill It was opened by another footman in livery, with a smile. There was exactly three inches high). 'But I'm not."
//                        ]
//                    ],
//                    "createdAt" => "2023-11-18T15:35:07+00:00",
//                    "updatedAt" => "2023-11-18T15:35:07+00:00",
//                    "isPublic" => false
//                ],
//            ],
//        ];

        return $this->json($data);
    }
}
