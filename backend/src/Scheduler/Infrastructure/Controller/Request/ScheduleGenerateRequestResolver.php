<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Controller\Request;

use App\Scheduler\Domain\Request\ScheduleGenerateRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ScheduleGenerateRequestResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === ScheduleGenerateRequest::class;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable<ScheduleGenerateRequest>
     * @throws \JsonException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $request = $this->serializer->deserialize($request->getContent(), ScheduleGenerateRequest::class, 'json');

        $errors = $this->validator->validate($request);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new BadRequestHttpException(json_encode(['errors' => $messages], JSON_THROW_ON_ERROR));
        }

        yield $request;
    }
}
