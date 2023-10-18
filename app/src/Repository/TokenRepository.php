<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Token;
use function base64_decode;
use function base64_encode;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends AbstractRepository
{
    public function encode(Token $token): string
    {
        return base64_encode($token->getUser()->getRawId() . ':' .  $token->getExpireAt()->getTimestamp() . ':' . $token->getRawId());
    }

    public function decode(string $code): ?Token
    {
        $decoded = base64_decode($code, true);
        if (!$decoded) {
            return null;
        }

        $data = explode(':', $decoded);
        if (count($data) !== 3) {
            return null;
        }
        $userRawId = $data[0];
        $tokenId = $data[2];

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
