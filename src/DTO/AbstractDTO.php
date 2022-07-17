<?php

declare(strict_types=1);

namespace App\DTO;

use BadMethodCallException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_fill_keys;
use function array_intersect_key;
use function array_keys;
use function get_class;
use function get_class_vars;
use function is_bool;
use function lcfirst;
use function property_exists;
use function sprintf;
use function strtolower;
use function substr;

abstract class AbstractDTO
{
    /**
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the
     *                                   specified validation rules
     * @throws MissingOptionsException   If a required option is missing
     */
    public function __construct(array $data, bool $ignoreExtra = false)
    {
        if ($ignoreExtra) {
            $data = array_intersect_key($data, array_fill_keys(array_keys(get_class_vars(static::class)), null));
        }

        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);

        foreach ($resolver->resolve($data) as $key => $item) {
            $this->$key = $item;
        }
    }

    public function __call(string $name, array $arguments)
    {
        // get property
        $method   = strtolower(substr($name, 0, 3));
        $property = lcfirst(substr($name, 3));
        if ('get' === $method && property_exists($this, $property)) {
            return $this->{$property};
        }

        // bool property
        $method   = strtolower(substr($name, 0, 2));
        $property = lcfirst(substr($name, 2));
        if ('is' === $method && property_exists($this, $property) && is_bool($this->{$property})) {
            return $this->{$property};
        }

        // by default throw exeption
        $message = sprintf('Undefined method "%s".', $name);
        throw new BadMethodCallException($message);
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->getClassProperties() as $property) {
            if ($this->{$property} instanceof self) {
                $result[$property] = $this->{$property}->toArray();
            } else {
                $result[$property] = $this->{$property};
            }
        }

        return $result;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined($this->getClassProperties());
    }

    protected function getClassProperties(): array
    {
        return array_keys(get_class_vars(get_class($this)));
    }

}
