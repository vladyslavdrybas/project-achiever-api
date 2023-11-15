<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AchievementList;
use App\Entity\User;
use App\Security\Permissions;
use function var_dump;

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
