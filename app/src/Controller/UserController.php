<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Transfer\UserEditTransfer;
use App\Transfer\UserPasswordChangeTransfer;
use App\ValueResolver\UserValueResolver;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;

#[Route('/user', name: "api_user")]
class UserController extends AbstractController
{
    #[Route("/{user}", name: "_profile", methods: ["GET"])]
    public function profile(
        #[ValueResolver(UserValueResolver::class)]
        User $user
    ): JsonResponse {
        /** @var \App\Entity\User $user */
        $owner = $this->getUser();
        if ($user === $owner) {
            $data = $this->serializer->normalize($user, User::class, [
                'custom_attributes' => [
                    'achievementsAmount'
                ],
            ]);
        } else {
            $data = $this->serializer->normalize($user, User::class, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'isEmailVerified',
                    'email',
                ],
                'custom_attributes' => [
                    'achievementsAmount'
                ],
            ]);
        }

        return $this->json($data);
    }

    #[Route("/{username}/info", name: "_info", methods: ["GET"])]
    public function info(
        string $username,
        UserRepository $userRepository
    ): JsonResponse {
        /** @var \App\Entity\User $user */
        $owner = $this->getUser();
        $user = $userRepository->loadUserByUsername($username);
        if (null === $user) {
            throw new NotFoundHttpException('User not found.');
        }

        if ($user === $owner) {
            $data = $this->serializer->normalize($user, User::class, [
                'custom_attributes' => [
                    'achievementsAmount'
                ],
            ]);
        } else {
            $data = $this->serializer->normalize($user, User::class, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'isEmailVerified',
                    'email',
                ],
                'custom_attributes' => [
                    'achievementsAmount'
                ],
            ]);
        }

        return $this->json($data);
    }

    #[Route("/{user}", name: "_edit", methods: ["PUT"])]
    public function edit(
        User $user,
        UserEditTransfer $userEditTransfer,
        UserRepository $userRepository
    ): JsonResponse {
        /** @var \App\Entity\User $user */
        $owner = $this->getUser();
        if ($user !== $owner) {
            throw new AccessDeniedHttpException('Access denied. You can edit only your own profile.');
        }

        if ($user->getUsername() !== $userEditTransfer->getUsername() && $userRepository->findOneBy(['username' => $userEditTransfer->getUsername()])) {
            throw new InvalidArgumentException(sprintf('Username %s exists. Chose another one.', $userEditTransfer->getUsername()));
        }

        if ($user->getEmail() !== $userEditTransfer->getEmail() && $userRepository->findOneBy(['email' => $userEditTransfer->getEmail()])) {
            throw new InvalidArgumentException(sprintf('Email %s exists. Chose another one.', $userEditTransfer->getEmail()));
        }

        $user->setUsername($userEditTransfer->getUsername());
        $user->setEmail($userEditTransfer->getEmail());
        $user->setFirstname($userEditTransfer->getFirstname());
        $user->setLastname($userEditTransfer->getLastname());

        $userRepository->add($user);
        $userRepository->save();

        $data = $this->serializer->normalize($user);

        return $this->json($data);
    }

    #[Route("/{user}/passwordchange", name: "_passwordchange", methods: ["PUT"])]
    public function passwordChange(
        User $user,
        UserPasswordChangeTransfer $userPasswordChangeTransfer,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        /** @var \App\Entity\User $user */
        $owner = $this->getUser();
        if ($user !== $owner) {
            throw new AccessDeniedHttpException('Access denied. You can edit only your own profile.');
        }

        $isValid = $passwordHasher->isPasswordValid(
            $user,
            $userPasswordChangeTransfer->getOldpassword()
        );

        if (!$isValid) {
            throw new AccessDeniedHttpException('Old password mismatch.');
        }

        if ($userPasswordChangeTransfer->getNewpassword() !== $userPasswordChangeTransfer->getConfirmpassword()) {
            throw new InvalidArgumentException('Password not confirmed.');
        }

        if (strlen($userPasswordChangeTransfer->getNewpassword()) < 5) {
            throw new InvalidArgumentException('Password length less than 5.');
        }

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $userPasswordChangeTransfer->getNewpassword()
        );

        $user->setPassword($hashedPassword);

        $userRepository->add($user);
        $userRepository->save();

        $data = $this->serializer->normalize($user);

        return $this->json($data);
    }

    #[Route("/list/public/{offset}/{limit}",
        name: "_list",
        requirements: ['offset' => '\d+', 'limit' => '1|2|3|4|5|10|20|50'],
        defaults: ['offset' => 0, 'limit' => 5],
        methods: ["GET"]
    )]
    public function list(
        int $offset,
        int $limit,
        UserRepository $userRepository
    ): JsonResponse {
        $users = $userRepository->findAll(['createdAt', 'DESC'], $offset, $limit);

        $data = $this->serializer->normalize($users, null, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => [
                'object',
                'isEmailVerified',
                'isBanned',
                'isDeleted',
                'locale',
                'email',
            ],
            'custom_attributes' => [
                'achievementsAmount'
            ],
        ]);

        return $this->json($data);
    }
}
