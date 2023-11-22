<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ShareObjectToken;

/**
 * @method ShareObjectToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShareObjectToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShareObjectToken[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method ShareObjectToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<ShareObjectToken>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method ShareObjectToken|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class ShareObjectTokenRepository extends AbstractRepository
{
}
