<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Achievement;

/**
 * @method Achievement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achievement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achievement[]    findAll()
 * @method Achievement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<Achievement>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method Achievement|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class AchievementRepository extends AbstractRepository
{
}
