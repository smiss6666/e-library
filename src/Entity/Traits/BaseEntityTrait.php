<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use function method_exists;
use function property_exists;
use function ucfirst;

trait BaseEntityTrait
{

    public static function create(array $data): self
    {
        $self = new static();
        $self->processAutoSet($data, $self);

        return $self;
    }

    public function update(array $data): self
    {
        $this->processAutoSet($data, $this);

        return $this;
    }

    protected function processAutoSet(array $data, $instance): void
    {
        foreach ($data as $key => $item) {
            if (method_exists($instance, 'set' . ucfirst($key))) {
                $instance->{'set' . ucfirst($key)}($item);
            } elseif (property_exists($instance, $key)) {
                $instance->$key = $item;
            }
        }
    }

}
