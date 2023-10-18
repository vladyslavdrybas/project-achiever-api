<?php

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Entity\User;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function findByEmail(string $identifier): ?User
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.email = :identifier')
            ->setParameter('identifier', $identifier);

        $result = $query->getQuery()->getOneOrNullResult();
        if ($result instanceof User) {
            return $result;
        }

        return null;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if ($user->getPassword() !== $newHashedPassword) {
            throw new Exception('new hashed password mismatch.');
        }

        if ($user instanceof EntityInterface) {
            $this->add($user);
            $this->save();
        }
    }
}
