<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Achievement;
use App\Entity\AchievementList;
use App\Entity\EntityInterface;
use App\Entity\User;
use DateTimeImmutable;

/**
 * @method Achievement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achievement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achievement[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method Achievement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<Achievement>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method Achievement|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class AchievementRepository extends AbstractRepository
{
    public function findByList(AchievementList $achievementList, $offset, $limit): array
    {
        return $this->createQueryBuilder('t')
            ->where(':list MEMBER OF t.lists')
            ->setParameter(':list', $achievementList)
            ->orderBy('t.doneAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findForUserByTimestamp(
        User $user,
        int $timestamp,
        int $offset,
        int $limit,
        int $timeRange = EntityInterface::TIME_RANGE_OLDER
    ): array {
        $createdAt = (new DateTimeImmutable())->setTimestamp($timestamp);

        $query = $this->createQueryBuilder('t')
            ->where('t.owner = :owner')
            ->andWhere('t.createdAt < :createdAt')
            ->setParameter('owner', $user)
            ->setParameter('createdAt', $createdAt)
            ->orderBy('t.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        if ($timeRange === EntityInterface::TIME_RANGE_OLDER) {
            $query->andWhere('t.createdAt < :createdAt');
        } else {
            $query->andWhere('t.createdAt > :createdAt');
        }

        return $query->getQuery()
            ->getResult();
    }

    public function findByUser(User $user, User $owner, $offset, $limit): array
    {
        $query = $this->createQueryBuilder('t')
            ->where(':list MEMBER OF t.lists')
            ->setParameter(':list', $user)
            ->orderBy('t.doneAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $query->getQuery()->getResult();
    }
}
