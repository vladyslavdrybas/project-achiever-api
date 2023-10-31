<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FirebaseCloudMessaging;

/**
 * @method FirebaseCloudMessaging|null find($id, $lockMode = null, $lockVersion = null)
 * @method FirebaseCloudMessaging|null findOneBy(array $criteria, array $orderBy = null)
 * @method FirebaseCloudMessaging[]    findAll()
 * @method FirebaseCloudMessaging[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<FirebaseCloudMessaging>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method FirebaseCloudMessaging|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class FirebaseCloudMessagingRepository extends AbstractRepository
{
}
