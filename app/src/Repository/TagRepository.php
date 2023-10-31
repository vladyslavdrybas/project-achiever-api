<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<Tag>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method Tag|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class TagRepository extends AbstractRepository
{
}
