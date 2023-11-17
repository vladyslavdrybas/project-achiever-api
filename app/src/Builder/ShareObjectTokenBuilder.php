<?php

declare(strict_types=1);

namespace App\Builder;

use App\Entity\Achievement;
use App\Entity\AchievementList;
use App\Entity\ShareObjectToken;
use App\Entity\User;
use App\Repository\ShareObjectTokenRepository;
use App\Security\AchievementSecurityManager;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ShareObjectTokenBuilder implements IEntityBuilder
{
    public function __construct(
        protected readonly AchievementSecurityManager $achievementSecurityManager,
        protected readonly UrlGeneratorInterface $urlGenerator,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly ShareObjectTokenRepository $shareObjectTokenRepository
    ) {}

    public function achievementShareObjectToken(
        Achievement $achievement,
        AchievementList $achievementList,
        User $owner,
        ?DateTimeInterface $expireAt = null,
        bool $isCanEdit = false
    ): ShareObjectToken {
        if (!$achievementList->getAchievements()->contains($achievement)) {
            throw new NotFoundHttpException('List does not contain chosen achievement.');
        }

        $token = new ShareObjectToken();
        $token->setOwner($owner);
        $token->setTarget('achievement');
        $token->setTargetId($achievement->getRawId());
        $token->setCanEdit($isCanEdit);

        $token->setId($token->generateId());
        if (null !== $expireAt) {
            $expireAt->setTimezone(new DateTimeZone('UTC'));
        }
        $token->setExpireAt($expireAt);

        $link = 'https:'. $this->urlGenerator->generate(
            'api_achievement_list_achievement_show',
            [
                'achievementList' => $achievementList->getRawId(),
                'achievement' => $achievement->getRawId(),
            ],
            UrlGeneratorInterface::NETWORK_PATH
        );
        $token->setLink($link);

        $token->setHash($token->generateHash());

        $tokenOld = $this->shareObjectTokenRepository->findOneBy([
            'hash' => $token->getHash(),
        ]);

        if ($tokenOld instanceof ShareObjectToken) {
            return $tokenOld;
        }

        return $token;
    }
}
