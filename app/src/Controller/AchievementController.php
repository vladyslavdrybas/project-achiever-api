<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\Tag;
use App\Repository\AchievementRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Transfer\AchievementCreateJsonTransfer;
use DateTimeZone;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use function array_map;

#[Route('/achievement', name: "achievement")]
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

        $achievement->setUser($user);

        $achievementRepository->add($achievement);
        $achievementRepository->save();

        $data = $this->serializer->normalize($achievement);

        return $this->json($data);
    }

    #[Route("/list/{userId}", name: "_list", methods: ["GET"])]
    public function list(): JsonResponse
    {
        $data = [];

        return $this->json($data);
    }

    #[Route("/{id}", name: "_show", methods: ["GET"])]
    public function show(
        string $id,
        AchievementRepository $achievementRepository
    ): JsonResponse {
        try {
            $achievement = $achievementRepository->find($id);

            if (!$achievement instanceof Achievement) {
                throw new Exception('not found');
            }
        } catch (Exception $e) {
            return $this->json(
                [
                    'message' => $e->getMessage(),
                ],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        $data = $this->serializer->normalize($achievement);

        return $this->json($data);
    }

    #[Route("/{id}", name: "_edit", methods: ["PUT"])]
    public function edit(): JsonResponse
    {
        $data = [];

        return $this->json($data);
    }

}
