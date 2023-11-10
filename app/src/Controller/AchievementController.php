<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\Tag;
use App\Repository\AchievementRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Transfer\AchievementCreateJsonTransfer;
use App\Transfer\AchievementEditJsonTransfer;
use App\Transfer\AchievementTagAttachJsonTransfer;
use DateTimeZone;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function array_map;
use function sprintf;

#[Route('/achievement', name: "api_achievement")]
class AchievementController extends AbstractController
{
    #[Route("/create", name: "_create", methods: ["POST"])]
    public function create(
        AchievementCreateJsonTransfer $transfer,
        AchievementRepository $achievementRepository,
        UserRepository $userRepository,
        TagRepository $tagRepository
    ): JsonResponse {
        $achievement = new Achievement();
        $achievement->setTitle($transfer->getTitle());
        $achievement->setDescription($transfer->getDescription());

        if (count($transfer->getTags()) === 0) {
            return $this->json(
                [
                    'message' => 'You should add at least one tag to achievement.',
                ],
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        $tags = array_map(function (string $tagId) use ($tagRepository) {
            $tag = new Tag();
            $tag->setId($tagId);

            $tagExist = $tagRepository->find($tag->getRawId());
            if (!$tagExist instanceof Tag) {
                $tagRepository->add($tag);
                $tagRepository->save();

                return $tag;
            }

            return $tagExist;
        }, $transfer->getTags());

        foreach ($tags as $tag) {
            $achievement->addTag($tag);
        }

        $doneAt = $transfer->getDoneAt()
            ?->setTimezone(
                new DateTimeZone('UTC')
            );
        $achievement->setDoneAt($doneAt);

        $user = $userRepository->findByEmail($this->getUser()->getUserIdentifier());

        $achievement->setOwner($user);

        $achievementRepository->add($achievement);
        $achievementRepository->save();

        $data = $this->serializer->normalize($achievement);

        return $this->json($data);
    }

    #[Route(
        "/list/{userId}/{offset}/{length}",
        name: "_list",
        requirements: ['offset' => '\d+', 'length' => '5|10|20|50'],
        defaults: ['offset' => 0, 'length' => 5],
        methods: ["GET"]
    )]
    public function list(
        string $userId,
        int $offset,
        int $length,
        AchievementRepository $achievementRepository
    ): JsonResponse {
        $achievements = $achievementRepository->findBy(
            [
                'user' => $userId,
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

    #[Route("/{achievement}", name: "_show", methods: ["GET"])]
    #[IsGranted('read', 'achievement', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function show(
        Achievement $achievement
    ): JsonResponse {
        return $this->json($this->serializer->normalize($achievement));
    }

    #[Route("/{achievement}", name: "_delete", methods: ["DELETE"])]
    #[IsGranted('update', 'achievement', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function delete(
        Achievement $achievement,
        AchievementRepository $achievementRepository
    ): JsonResponse {
        $achievementRepository->remove($achievement);
        $achievementRepository->save();

        return $this->json([
            "message" => "success",
        ]);
    }

    #[Route("/{achievement}", name: "_edit", methods: ["PUT"])]
    #[IsGranted('update', 'achievement', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function edit(
        Achievement $achievement,
        AchievementEditJsonTransfer $transfer,
        AchievementRepository $achievementRepository
    ): JsonResponse {
        $achievement->setTitle($transfer->getTitle());
        $achievement->setDescription($transfer->getDescription());
        $achievement->setIsPublic($transfer->isPublic());
        $doneAt = $transfer->getDoneAt()
            ?->setTimezone(
                new DateTimeZone('UTC')
            );
        $achievement->setDoneAt($doneAt);

        $achievementRepository->add($achievement);
        $achievementRepository->save();

        $data = $this->serializer->normalize($achievement);

        return $this->json($data);
    }

    #[Route("/{achievement}/tag/detach", name: "_detach_tag", methods: ["PUT"])]
    #[IsGranted('update', 'achievement', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function removeTag(
        Achievement $achievement,
        AchievementTagAttachJsonTransfer $tagAttachJsonTransfer,
        AchievementRepository $achievementRepository,
        TagRepository $tagRepository
    ): JsonResponse {
        foreach ($tagAttachJsonTransfer->getTags() as $tagId) {
            $checkTag = new Tag();
            $checkTag->setId($tagId);
            $tag = $tagRepository->find($checkTag->getRawId());
            if ($tag instanceof Tag) {
                $achievement->removeTag($tag);
            }
        }

        $achievementRepository->add($achievement);
        $achievementRepository->save();

        $achievement = $achievementRepository->find($achievement->getId());

        $data = $this->serializer->normalize($achievement);

        return $this->json($data);
    }

    #[Route("/{achievement}/tag/attach", name: "_attach_tag", methods: ["PUT"])]
    #[IsGranted('update', 'achievement', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function addTag(
        Achievement $achievement,
        AchievementTagAttachJsonTransfer $tagAttachJsonTransfer,
        AchievementRepository $achievementRepository,
        TagRepository $tagRepository
    ): JsonResponse {
        $addLength = count($tagAttachJsonTransfer->getTags());
        $hasLength = $achievement->getTags()->count();
        $expectedLength = $hasLength + $addLength;
        $maxLength = 10;
        if ($expectedLength >= $maxLength) {
            return $this->json(
                [
                    'message' => sprintf(
                        'Max amount of tags is %s. You have %s. Restricted to add %s more.',
                        $maxLength,
                        $hasLength,
                        $addLength
                    ),
                ],
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        foreach ($tagAttachJsonTransfer->getTags() as $tagId) {
            $checkTag = new Tag();
            $checkTag->setId($tagId);
            $tag = $tagRepository->find($checkTag->getRawId());
            if (!$tag instanceof Tag) {
                $tag = $checkTag;
                $tagRepository->add($tag);
                $tagRepository->save();
            }

            $achievement->addTag($tag);
        }

        $achievementRepository->add($achievement);
        $achievementRepository->save();

        $data = $this->serializer->normalize($achievement);

        return $this->json($data);
    }

    #[Route("/{achievement}/tag/replace", name: "_replace_tag", methods: ["PUT"])]
    #[IsGranted('update', 'achievement', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function replaceTag(
        Achievement $achievement,
        AchievementTagAttachJsonTransfer $tagAttachJsonTransfer,
        AchievementRepository $achievementRepository,
        TagRepository $tagRepository
    ): JsonResponse {
        $replaceLength = count($tagAttachJsonTransfer->getTags());
        $maxLength = 10;
        if ($replaceLength >= $maxLength) {
            return $this->json(
                [
                    'message' => sprintf(
                        'Max amount of tags is %s. Restricted to replace it by %s.',
                        $maxLength,
                        $replaceLength
                    ),
                ],
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        if ($replaceLength === 0) {
            return $this->json(
                [
                    'message' => sprintf(
                        'You should add at least one tag to achievement.',
                    ),
                ],
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        $tagsToSave = [];

        foreach ($tagAttachJsonTransfer->getTags() as $tagName) {
            $tag = new Tag();
            $tag->setId($tagName);
            $tagsToSave[] = $tag;
        }

        foreach ($achievement->getTags() as $achievementTag) {
            $keep = false;
            foreach ($tagsToSave as $key => $tag) {
                if ($tag->getRawId() === $achievementTag->getRawId()) {
                    $keep = true;
                    unset($tagsToSave[$key]);
                    break;
                }
            }
            if (!$keep) {
                $achievement->removeTag($achievementTag);
            }
        }

        foreach ($tagsToSave as $tag) {
            $checkTag = $tagRepository->find($tag->getRawId());
            if (!$checkTag instanceof Tag) {
                $tagRepository->add($tag);
                $tagRepository->save();
                $achievement->addTag($tag);
            } else {
                $achievement->addTag($checkTag);
            }
        }

        $achievementRepository->add($achievement);
        $achievementRepository->save();

        $data = $this->serializer->normalize($achievement);

        return $this->json($data);
    }
}
