<?php

declare(strict_types=1);

namespace App\Repository;


use App\Entity\AnalyticsTrackNotification;

/**
 * @method AnalyticsTrackNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyticsTrackNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyticsTrackNotification[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method AnalyticsTrackNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<AnalyticsTrackNotification>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method AnalyticsTrackNotification|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class AnalyticsTrackNotificationRepository extends AbstractRepository
{
}
