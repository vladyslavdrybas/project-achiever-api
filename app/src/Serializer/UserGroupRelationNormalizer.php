<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\UserGroupRelation;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserGroupRelationNormalizer extends AbstractEntityNormalizer
{
    /**
     * @param UserGroupRelation $object
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize(
            $object,
            $format,
            [
                AbstractNormalizer::CALLBACKS => [
                    'member' => [$this, 'normalizeUserInList'],
                    'userGroup' => [$this, 'normalizeWithIdOnly'],
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawId',
                    'canView',
                    'canEdit',
                    'canDelete',
                    'canManage',
                    'createdAt',
                    'updatedAt',
                ],
            ]
        );

//        foreach (UserGroupPermissions::ROLES as $role => $permissions) {
//            if (
//                $permissions['view'] === $object->isCanView() &&
//                $permissions['edit'] === $object->isCanEdit() &&
//                $permissions['delete'] === $object->isCanDelete() &&
//                $permissions['manage'] === $object->isCanManage()
//            ) {
//                $data['role'] = $role;
//                break;
//            }
//        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof UserGroupRelation;
    }
}
