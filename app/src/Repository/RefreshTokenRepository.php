<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RefreshToken;
use App\Entity\UserInterface;
use Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenRepositoryInterface;

/**
 * @method RefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Doctrine\Common\Collections\Collection<RefreshToken>    matching(\Doctrine\Common\Collections\Criteria $criteria)
 * @method RefreshToken|null    matchingOneOrNull(\Doctrine\Common\Collections\Criteria $criteria)
 */
class RefreshTokenRepository extends AbstractRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @param \App\Entity\UserInterface $user
     * @return \App\Entity\RefreshToken[]
     */
    public function findAllByUser(UserInterface $user): array
    {
        return $this->findBy(['username' => $user->getUserIdentifier()]);
    }

    /**
     * @param \App\Entity\UserInterface $user
     * @return void
     */
    public function removeAllByUser(UserInterface $user): void
    {
        $tokens = $this->findAllByUser($user);
        if (count($tokens) > 0) {
            foreach ($tokens as $token) {
                $this->remove($token);
            }

            $this->save();
        }
    }

    /**
     * @param \DateTimeInterface|null $datetime
     * @return RefreshToken[]
     */
    public function findInvalid($datetime = null)
    {
        $datetime = (null === $datetime) ? new \DateTime() : $datetime;

        return $this->createQueryBuilder('u')
            ->where('u.valid < :datetime')
            ->setParameter(':datetime', $datetime)
            ->getQuery()
            ->getResult();
    }
}
