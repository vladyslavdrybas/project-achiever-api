<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

class UserValueResolver implements ValueResolverInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $em
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // get the argument type (e.g. BookingId)
        $argumentType = $argument->getType();
        if ($argumentType !== User::class) {
            return [];
        }

        // get the value from the request, based on the argument name
        $value = $request->attributes->get($argument->getName());
        if (!is_string($value)) {
            return [];
        }

        if (Uuid::isValid($value)) {
            $user = $this->em->getRepository(User::class)->find($value);
        } else {
            $user = $this->em->getRepository(User::class)->loadUserByUsername($value);
        }

        if ($user instanceof User) {
            return [$user];
        }

        // create and return the value object
        return [];
    }
}
