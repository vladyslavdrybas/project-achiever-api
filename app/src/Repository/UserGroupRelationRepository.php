<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserGroup;
use App\Entity\UserGroupRelation;

/**
 * @method UserGroupRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroupRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroupRelation[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method UserGroupRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<UserGroupRelation>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method UserGroupRelation|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class UserGroupRelationRepository extends AbstractRepository
{
    public function findAllByGroup(UserGroup $group, $offset, $limit): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.userGroup = :group')
            ->setParameter(':group', $group)
            ->orderBy('t.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
