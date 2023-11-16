<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AchievementPrerequisiteRelation;
use App\Repository\AchievementPrerequisiteRelationRepository;
use App\Security\Permissions;
use App\Transfer\AchievementPrerequisiteRelationTransfer;
use InvalidArgumentException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/achievement/prerequisite', name: "api_achievement_prerequisite")]
class AchievementPrerequisiteController extends AbstractController
{
    #[Route("", name: "_create", methods: ["POST"])]
    public function create(
        AchievementPrerequisiteRelationTransfer $transfer,
        AchievementPrerequisiteRelationRepository $repository,
        Security $security
    ): JsonResponse {
        if (!$security->isGranted(Permissions::VIEW, $transfer->getPrerequisite())) {
            throw new AccessDeniedHttpException('Access denied to prerequisite.');
        }

        if (!$security->isGranted(Permissions::EDIT, $transfer->getAchievement())) {
            throw new AccessDeniedHttpException('Permissions restricted to attach prerequisite to achievement.');
        }

        if ($transfer->getPrerequisite() === $transfer->getAchievement()) {
            throw new InvalidArgumentException('Prerequisite cannot reference on itself.');
        }

        $relation = new AchievementPrerequisiteRelation();
        $relation->setAchievement($transfer->getAchievement());
        $relation->setPrerequisite($transfer->getPrerequisite());
        $relation->setPriority($transfer->getPriority());
        $relation->setCondition($transfer->getCondition());
        $relation->setIsRequired($transfer->isRequired());

        $repository->add($relation);
        $repository->save();

        return $this->success();
    }
}
