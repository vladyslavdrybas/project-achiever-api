<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserGroup;

/**
 * @method UserGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroup[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method UserGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<UserGroup>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method UserGroup|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class UserGroupRepository extends AbstractRepository
{
}
