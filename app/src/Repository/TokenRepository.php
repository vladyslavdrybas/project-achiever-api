<?php

namespace App\Repository;

use App\Entity\Token;
use App\Entity\User;
use DateTimeImmutable;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends AbstractRepository
{
    public function generateForUser(User $user): Token
    {
        $token = new Token();
        $id = hash('sha256',bin2hex(random_bytes(5)) . $user->getRawId() . time());
        $token->setId($id);
        $token->setUser($user);
        $token->setExpireAt(new DateTimeImmutable('+5 minutes'));

        return $token;
    }

    public function encode(Token $token): string
    {
        return base64_encode($token->getUser()->getRawId() . ':' . $token->getRawId());
    }

    public function decode(string $code): ?Token
    {
        $decoded = base64_decode($code, true);
        if (!$decoded) {
            return null;
        }

        $data = explode(':', $decoded);
        if (count($data) !== 2) {
            return null;
        }

        $userRawId = $data[0];
        $tokenId = $data[1];

        $token = $this->findOneBy([
            'user' => $userRawId,
            'id' => $tokenId,
        ]);

        if ($token instanceof Token) {
            return $token;
        }

        return null;
    }
}
