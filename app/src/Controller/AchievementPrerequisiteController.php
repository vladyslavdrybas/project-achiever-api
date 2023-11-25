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

    #[Route("/tree/{achievement}", name: "_a_prerequisite_tree", methods: ["GET"])]
    public function atreePrerequisite(
        Achievement $achievement
    ): JsonResponse {
//        $tree = $this->buildPrerequisiteTree($achievement);

        $tree = [
            "id" => "66a41678-cb1f-4b5a-a9b4-246bdef37122",
            "title" => "Alice had never been so much contradicted in her head, she tried to fancy to herself how she would get up and rubbed its.",
            "prerequisites" => [
                [
                    "id" => "d2d96c54-00fb-4746-9b25-27bc7adecc1e",
                    "title" => "The table was a general chorus of voices asked. 'Why, SHE, of course,' said the Caterpillar. 'Well, I've tried banks, and.",
                    "priority" => 0,
                    "isRequired" => "yes",
                    "lists" => [
                        [
                            "id" => "0c3edbb1-2a20-46ed-9557-6a9dee727eb7",
                            "title" => "And then, turning to the Gryphon. 'Well, I hardly know--No more, thank ye; I'm better now--but I'm a deal too flustered to."
                        ],
                        [
                            "id" => "ef62d70f-d834-4d01-bfd7-c90d3b1bd59f",
                            "title" => "Little Bill It was opened by another footman in livery, with a smile. There was exactly three inches high). 'But I'm not."
                        ]
                    ]
                ],
                [
                    "id" => "aeff5afa-3a43-43cd-ae67-5eabe6d55265",
                    "title" => "The moment Alice appeared, she was as much as she spoke. Alice did not like to be a grin, and she felt certain it must be.",
                    "priority" => 1,
                    "isRequired" => "no",
                    "lists" => [
                        [
                            "id" => "0c3edbb1-2a20-46ed-9557-6a9dee727eb7",
                            "title" => "And then, turning to the Gryphon. 'Well, I hardly know--No more, thank ye; I'm better now--but I'm a deal too flustered to."
                        ],
                        [
                            "id" => "ef62d70f-d834-4d01-bfd7-c90d3b1bd59f",
                            "title" => "Little Bill It was opened by another footman in livery, with a smile. There was exactly three inches high). 'But I'm not."
                        ]
                    ]
                ]
            ]
        ];


        return $this->json($tree);
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
