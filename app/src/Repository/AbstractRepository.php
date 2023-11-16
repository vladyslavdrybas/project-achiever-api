<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use function str_replace;

/**
 * @method EntityInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntityInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntityInterface[]    findAll()
 * @method EntityInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            $message = $e->getMessage();
            if (str_contains($message, 'duplicate key')) {
                $message = 'Already exists.';
                throw new DuplicateKeyException($message);
            } else {
                throw $e;
            }
        }
    }

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     * @return \Doctrine\Common\Collections\Collection
     */
    public function matching(Criteria $criteria): Collection
    {
        return parent::matching($criteria);
    }

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     * @return \App\Entity\EntityInterface|null
     */
    public function matchingOneOrNull(Criteria $criteria): ?EntityInterface
    {
        $matched = parent::matching($criteria);

        return $matched->isEmpty() ? null : $matched->first();
    }
}
