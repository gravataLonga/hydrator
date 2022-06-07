<?php declare(strict_types=1);

namespace Gravatalonga\Hydrator;

/**
 * Class Hydrator
 *
 * @package Gravatalonga\Hydrator
 */
final class Hydrator
{
    /**
     * @var array array<string, mixed>
     */
    private array $data;

    private object $object;

    /**
     * Hydrate constructor
     *
     * @param array array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @throws HydratorException
     */
    public function hydrate(object $object): object
    {
        $this->object = $object;
        foreach ($this->data as $key => $value) {
            $this->populateData($key, $value);
        }

        return $this->object;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws HydratorException
     */
    private function populateData(string $key, $value)
    {
        if (! property_exists($this->object, $key)) {
            return;
        }

        if (! $this->isPublic($key)) {
            throw HydratorException::cannotPopulateNonPublicProperty($key);
        }

        $this->object->{$key} = $this->formatValue($key, $value);
    }

    private function formatValue(string $key, $value)
    {
        if ($this->hasCustomFormat($key)) {
            return $this->customFormat($key, $value);
        }

        $dataType = $this->dataType($key);

        if (empty($dataType)) {
            return $value;
        }

        if ($dataType === 'int') {
            return (int)$value;
        }

        if ($dataType === 'string') {
            return (string)$value;
        }

        if ($dataType === 'bool') {
            return ! empty($value);
        }

        if ($dataType === 'float') {
            return (float)$value;
        }

        return $value;
    }

    private function isPublic(string $key): bool
    {
        $reflection = new \ReflectionObject($this->object);
        $property = $reflection->getProperty($key);
        $property->setAccessible(true);

        return $property->isPublic();
    }

    private function dataType(string $key): string
    {
        $reflection = new \ReflectionObject($this->object);
        $property = $reflection->getProperty($key);
        if (is_null($property->getType())) {
            return '';
        }

        return ($property->getType())->getName();
    }

    private function hasCustomFormat(string $key): bool
    {
        return method_exists($this->object, $this->methodNameFormat($key));
    }

    private function customFormat(string $key, $value)
    {
        return $this->object->{$this->methodNameFormat($key)}($value);
    }

    private function methodNameFormat(string $propertyName): string
    {
        return sprintf('format%s', ucfirst($propertyName));
    }
}
