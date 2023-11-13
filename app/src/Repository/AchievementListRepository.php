<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AchievementList;

/**
 * @method AchievementList|null find($id, $lockMode = null, $lockVersion = null)
 * @method AchievementList|null findOneBy(array $criteria, array $orderBy = null)
 * @method AchievementList[]    findAll()
 * @method AchievementList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<AchievementList>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method AchievementList|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class AchievementListRepository extends AbstractRepository
{
}
