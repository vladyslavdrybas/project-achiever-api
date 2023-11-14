<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\AchievementList;
use App\Entity\ShareObjectToken;
use App\Repository\ShareObjectTokenRepository;
use App\Transfer\ShareObjectTokenJsonTransfer;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function ucfirst;

#[Route('/share/object/token', name: 'api_share_object_token')]
class ShareObjectTokenController extends AbstractController
{
    #[Route(
        '',
        name: "_create",
        methods: ['POST']
    )]
    public function create(
        ShareObjectTokenJsonTransfer $transfer,
        ShareObjectTokenRepository $tokenRepository,
        Security $security
    ): JsonResponse {
        $targetRepository = match($transfer->getTarget()) {
            'achievement' => $this->entityManager->getRepository(Achievement::class),
            default => throw new NotFoundHttpException('Target type not found.'),
        };

        $subject = $targetRepository->find($transfer->getTargetId());

        if (null === $subject) {
            throw new NotFoundHttpException(ucfirst($transfer->getTarget()) . ' not found.');
        }

        if (!$security->isGranted('edit', $subject)) {
            throw new AccessDeniedHttpException('Permissions restricted to create link for chosen ' . ucfirst($transfer->getTarget()));
        }

        $owner = $this->getUser();

        $token = new ShareObjectToken();
        $token->setOwner($owner);
        $token->setTarget($transfer->getTarget());
        $token->setTargetId($subject->getRawId());
        if ($transfer->getExpireAt()) {
            $expireAt = new DateTime($transfer->getExpireAt());
            $expireAt->setTimezone(new DateTimeZone('UTC'));
            $token->setExpireAt($expireAt);
        }
        $token->setCanView($transfer->isCanView());
        $token->setCanEdit($transfer->isCanEdit());
        $token->setId($token->generateId());

        $list = null;
        if (null !== $transfer->getAchievementListId()) {
            $list = $this->entityManager->getRepository(AchievementList::class)->find($transfer->getAchievementListId());
            if (!$list instanceof AchievementList) {
                throw new NotFoundHttpException('List does not found.');
            }

            if ($token->getTarget() === 'achievement') {
                if (!$list->getAchievements()->contains($subject)) {
                    throw new Exception('List does not contain chosen achievement.');
                }
            }
        }

        $link = match($token->getTarget()) {
            'achievement' => $this->urlGenerator->generate(
                'api_achievement_list_achievement_show',
                [
                    'achievementList' => $list?->getRawId(),
                    'achievement' => $token->getTargetId(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            default => null
        };
        $token->setLink($link);

        $tokenRepository->add($token);
        $tokenRepository->save();

        $data = [
            'token' => $token->getRawId(),
            'link' => $token->getLinkWithToken(),
        ];

        return $this->json($data);
    }
}
