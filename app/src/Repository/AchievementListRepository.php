<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AchievementList;
use App\Entity\EntityInterface;
use App\Entity\User;
use App\Security\Permissions;
use DateTimeImmutable;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method AchievementList|null find($id, $lockMode = null, $lockVersion = null)
 * @method AchievementList|null findOneBy(array $criteria, array $orderBy = null)
 * @method AchievementList[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method AchievementList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<AchievementList>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method AchievementList|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class AchievementListRepository extends AbstractRepository
{

    public function findOwnedLists(
        User $user,
        int $timestamp,
        int $offset,
        int $limit,
        int $timeRange = EntityInterface::TIME_RANGE_OLDER
    ): array {
        $createdAt = (new DateTimeImmutable())->setTimestamp($timestamp);

         $query = $this->createQueryBuilder('t')
            ->where('t.owner = :owner')
            ->setParameter('owner', $user)
            ->setParameter('createdAt', $createdAt)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('t.createdAt', 'DESC')
        ;

         if ($timeRange === EntityInterface::TIME_RANGE_OLDER) {
             $query->andWhere('t.createdAt < :createdAt');
         } else {
             $query->andWhere('t.createdAt > :createdAt');
         }

        return $query->getQuery()
            ->getResult();
    }

    public function findShareLists(
        User $user,
        int $timestamp,
        int $offset,
        int $limit,
        int $timeRange = EntityInterface::TIME_RANGE_OLDER
    ): array {
        $createdAt = (new DateTimeImmutable())->setTimestamp($timestamp);

        $query =  $this->createQueryBuilder('t')
            ->join('t.listGroupRelations', 'tug')
            ->join('tug.userGroupRelations', 'tugr')
            ->where('tugr.member = :member')
            ->setParameter('member', $user)
            ->setParameter('createdAt', $createdAt)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('t.createdAt', 'DESC')
        ;

        if ($timeRange === EntityInterface::TIME_RANGE_OLDER) {
            $query->andWhere('t.createdAt < :createdAt');
        } else {
            $query->andWhere('t.createdAt > :createdAt');
        }

        return $query->getQuery()
            ->getResult();
    }

    /**
     * @param \App\Entity\AchievementList $achievementList
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function findMembers(AchievementList $achievementList, int $offset, int $limit): array
    {
        $query = $this->createQueryBuilder('t')
//            ->select([
//                't.id as listId',
//                't.title as listTitle',
//                'IDENTITY(t.owner) as listOwnerId',
//                'tug.id as groupId',
//                'IDENTITY(tug.owner) as groupOwnerId',
//                'tug.title as groupTitle',
//                'members.id as memberId',
//            ])
            ->select(['members'])
            ->join('t.listGroupRelations', 'tug')
            ->join('tug.userGroupRelations', 'tugr')
            ->join(User::class, 'members', Join::WITH, 'members.id = tugr.member')
            ->where('t.id = :list')
            ->setParameter('list', $achievementList)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('members.id', 'DESC')
        ;

        return $query->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param \App\Entity\AchievementList $achievementList
     * @param \App\Entity\User $user
     * @param string $permission
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isUserHasPermission(
        AchievementList $achievementList,
        User $user,
        string $permission
    ): bool {
        if ($achievementList->getOwner() === $user) {
            return true;
        }

        $query = $this->createQueryBuilder('t')
            ->select(['t.id as list_id'])
            ->join('t.listGroupRelations', 'tug')
            ->join('tug.userGroupRelations', 'tugr')
            ->where('t.id = :list')
            ->andWhere('tugr.member = :member')
        ;

        switch ($permission) {
            case Permissions::VIEW:
                $query->andWhere('tugr.canView = true');
                break;
            case Permissions::EDIT:
                $query->andWhere('tugr.canEdit = true');
                break;
            case Permissions::DELETE:
                $query->andWhere('tugr.canDelete = true');
                break;
            case Permissions::MANAGE:
                $query->andWhere('tugr.canManage = true');
                break;
            default:
                return false;
        }

        $query->setParameter('list', $achievementList)
            ->setParameter('member', $user);

        return null !== $query->getQuery()->getOneOrNullResult();
    }
}
