<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Security\Permissions;
use App\Security\UserGroupManager;
use App\Transfer\UserGroupCreateJsonTransfer;
use App\Transfer\UserGroupEditJsonTransfer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user/group', name: "api_user_group")]
class UserGroupController extends AbstractController
{
    #[Route("/create", name: "_create", methods: ["POST"])]
    public function create(
        UserGroupCreateJsonTransfer $createTransfer,
        UserGroupManager            $groupManager
    ): JsonResponse {
        $owner = $this->getUser();

        $group = $groupManager->createGroup($createTransfer->getTitle(), $createTransfer->getDescription(), $owner);

        return $this->json($this->serializer->normalize($group));
    }

    #[Route("/{group}", name: "_show", methods: ["GET"])]
    #[IsGranted(Permissions::VIEW, 'group', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function show(
        UserGroup $group
    ): JsonResponse {
        return $this->json($this->serializer->normalize($group));
    }

    #[Route("/{group}", name: "_edit", methods: ["PUT"])]
    #[IsGranted(Permissions::EDIT, 'group', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function edit(
        UserGroup $group,
        UserGroupEditJsonTransfer $editJsonTransfer,
        UserGroupManager            $groupManager
    ): JsonResponse {
        $group = $groupManager->editGroup($editJsonTransfer->getTitle(), $editJsonTransfer->getDescription(), $group, $this->getUser());

        return $this->json($this->serializer->normalize($group));
    }

    #[Route("/{group}/{user}/{role}", name: "_add_user", methods: ["POST"])]
    #[IsGranted(Permissions::MANAGE_MEMBERS, 'group', 'Access denied', JsonResponse::HTTP_UNAUTHORIZED)]
    public function addUserToGroup(
        UserGroup $group,
        User $user,
        string $role,
        UserGroupManager $groupManager
    ): JsonResponse {
        $groupManager->addUserToGroup($group, $user, $this->getUser(), $role);

        return $this->json([
            'message' => 'success',
        ]);
    }
}
