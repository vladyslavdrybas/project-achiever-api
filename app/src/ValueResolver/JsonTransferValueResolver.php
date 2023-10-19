<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Transfer\TransferInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

use function class_exists;
use function class_implements;
use function var_dump;

class JsonTransferValueResolver implements ValueResolverInterface
{
    protected SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        if (
            $className === null
            || !$request->getContent()
            || $request->getContentTypeFormat() != 'json'
            || !class_exists($className)
            || !\in_array(
                TransferInterface::class,
                class_implements($className) ?: [],
                true
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument
     * @return \Generator
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        if (false === $this->supports($request, $argument)) {
            return;
        }

        yield $this->serializer->deserialize(
            $request->getContent(),
            $argument->getType() ?? '',
            'json'
        );
    }
}
