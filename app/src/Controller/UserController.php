<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Transfer\UserEditTransfer;
use App\Transfer\UserPasswordChangeTransfer;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use function var_dump;

#[Route('/user', name: "api_user")]
class UserController extends AbstractController
{
    #[Route("/{user}", name: "_view", methods: ["GET"])]
    public function profile(
        User $user
    ): JsonResponse {
        /** @var \App\Entity\User $user */
        $owner = $this->getUser();
        if ($user === $owner) {
            $data = $this->serializer->normalize($user);
        } else {
            $cleanedUser = new User();
            $cleanedUser->setId($user->getId());
            $cleanedUser->setEmail('');
            $cleanedUser->setPassword('');
            $cleanedUser->setLocale($user->getLocale());
            $cleanedUser->setUsername($user->getUsername());
            $cleanedUser->setIsActive($user->isActive());
            $cleanedUser->setIsBanned($user->isBanned());
            $cleanedUser->setIsDeleted($user->isDeleted());
            $cleanedUser->setIsEmailVerified($user->isEmailVerified());

            $data = $this->serializer->normalize($cleanedUser);
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
}
