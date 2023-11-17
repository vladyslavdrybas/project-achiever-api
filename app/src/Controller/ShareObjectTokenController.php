<?php

declare(strict_types=1);

namespace App\Controller;

use App\Builder\ShareObjectTokenBuilder;
use App\Entity\AchievementList;
use App\Repository\AchievementRepository;
use App\Repository\ShareObjectTokenRepository;
use App\Security\Permissions;
use App\Transfer\ShareObjectTokenJsonTransfer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
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
        Security $security,
        ShareObjectTokenBuilder $shareObjectTokenBuilder,
        AchievementRepository $achievementRepository,
    ): JsonResponse {
        if ('achievement' !== $transfer->getTarget()) {
            throw new NotFoundHttpException('Target type not found.');
        }

        $achievement = $achievementRepository->find($transfer->getTargetId());

        if (null === $achievement) {
            throw new NotFoundHttpException(ucfirst($transfer->getTarget()) . ' not found.');
        }

        if (!$security->isGranted(Permissions::EDIT, $achievement)) {
            throw new AccessDeniedHttpException(
                'Permissions restricted to create view link for chosen '
                . ucfirst($transfer->getTarget())
            );
        }

        $achievementList = $this->entityManager->getRepository(AchievementList::class)
            ->find($transfer->getAchievementListId());

        if (!$achievementList instanceof AchievementList) {
            throw new NotFoundHttpException('List does not found.');
        }

        $owner = $this->getUser();

        $token = $shareObjectTokenBuilder->achievementShareObjectToken(
            $achievement,
            $achievementList,
            $owner,
            $transfer->getExpireAt()
        );

        if ($transfer->isCanEdit()) {
            if (!$security->isGranted(Permissions::MANAGE, $achievement)) {
                throw new AccessDeniedHttpException('Permissions restricted to create edit link for chosen ' . ucfirst($transfer->getTarget()));
            }

            $token->setCanEdit($transfer->isCanEdit());
        }

        $tokenRepository->add($token);
        $tokenRepository->save();

        $data = [
            'token' => $token->getRawId(),
            'link' => $token->getLinkWithToken(),
        ];

        return $this->json($data);
    }
}
