<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\AchievementList;
use App\Entity\ShareObjectToken;
use App\Entity\User;
use App\Entity\Achievement;
use App\Repository\AchievementListRepository;
use App\Repository\ShareObjectTokenRepository;
use App\Security\Permissions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use function var_dump;

final class AchievementVoter extends AbstractVoter
{
    protected Request $request;
    protected ?AchievementList $achievementList = null;

    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly ShareObjectTokenRepository $tokenRepository,
        protected readonly AchievementListRepository $achievementListRepository
    ) {
        $this->request = $requestStack->getCurrentRequest();
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // only vote on `Achievement` objects
        if (!$subject instanceof Achievement) {
            return false;
        }

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            Permissions::VIEW,
            Permissions::EDIT,
            Permissions::DELETE,
            Permissions::MANAGE,
        ])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Achievement $subject */

        if (!$this->setAchievementList($subject)) {
            return false;
        }

        if ($this->isAchievementPublicView($subject, $attribute)
            || $this->isAchievementListPublicView($this->achievementList, $attribute)
            || $this->isGrantedByAchievementShareToken($subject, $attribute)
        ) {
            return true;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->isOwner($subject, $user)) {
            return true;
        }

        return $this->isUserHasPermissionInList($attribute, $user);
    }

    protected function setAchievementList(Achievement $subject): bool
    {
        $routeParams = $this->request->attributes->all('_route_params');

        $listId = $routeParams['achievementList']
            ?? $this->request->getPayload()->get('achievementListId')
            ?? $routeParams['prerequisiteList']
            ?? $this->request->getPayload()->get('prerequisiteList')
        ;
        if (null === $listId) {
            return false;
        }

        foreach ($subject->getLists() as $list) {
            /** @var \App\Entity\AchievementList $list */
            if ($list->getRawId() === $listId) {
                $this->achievementList = $list;
                break;
            }
        }

        return null !== $this->achievementList;
    }

    protected function isAchievementPublicView(Achievement $subject, string $attribute): bool
    {
        return Permissions::VIEW === $attribute && $subject->isPublic();
    }

    protected function isAchievementListPublicView(?AchievementList $subject, string $attribute): bool
    {
        return Permissions::VIEW === $attribute && $subject?->isPublic();
    }

    protected function isGrantedByAchievementShareToken(Achievement $subject, string $attribute): bool
    {
        $tokenId = $this->request->query->get(ShareObjectToken::QUERY_IDENTIFIER);
        if (null === $tokenId) {
            return false;
        }

        $token = $this->tokenRepository->find($tokenId);

        if (
            !$token instanceof ShareObjectToken
            || 'achievement' !== $token->getTarget()
            || $token->getTargetId() !== $subject->getRawId()
            || (null !== $token->getExpireAt() && $token->getExpireAt()->getTimestamp() < time())
        ) {
            return false;
        }

        return match ($attribute) {
            Permissions::VIEW => true,
            Permissions::EDIT => $token->isCanEdit(),
            default => false
        };
    }

    protected function isUserHasPermissionInList(string $attribute, User $user): bool
    {
        return $this->achievementListRepository->isUserHasPermission($this->achievementList, $user, $attribute);
    }
}
