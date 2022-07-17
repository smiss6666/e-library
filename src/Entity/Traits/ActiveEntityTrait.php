<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ActiveEntityTrait
{
    /**
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default": 1})
     */
    protected bool $active = true;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }
}
