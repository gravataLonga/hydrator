<?php declare(strict_types=1);

namespace Gravatalonga\Hydrator;

final class HydratorException extends \Exception
{
    public static function cannotPopulateNonPublicProperty(string $property)
    {
        return new self(sprintf('property %s is private/protected and can not be hydrable', $property));
    }
}
