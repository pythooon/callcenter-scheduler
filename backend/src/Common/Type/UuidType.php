<?php

declare(strict_types=1);

namespace App\Common\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Uid\Uuid;

class UuidType extends Type
{
    public const NAME = 'uuid';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'BINARY(16)';
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value instanceof Uuid) {
            return $value->toBinary();
        }

        return $value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        if (!$value instanceof Uuid && is_string($value)) {
            return Uuid::fromBinary($value);
        }

        return $value;
    }
}
