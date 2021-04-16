<?php declare(strict_types=1);

namespace Gravatalonga\Hydrator;

trait HydrateTrait
{
    public static function hydrate(array $data): object
    {
        $m = new self;
        return (new Hydrator($data))->hydrate($m);
    }
}
