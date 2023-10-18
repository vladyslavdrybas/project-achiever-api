<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;
use function str_replace;

abstract class AbstractRepository extends ServiceEntityRepository implements Selectable
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityClass = substr(str_replace('Repository', 'Entity', static::class), 0,-6);
        /** @phpstan-ignore-next-line */
        parent::__construct($registry, $entityClass);
    }

    public function add(EntityInterface $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove(EntityInterface $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
}
