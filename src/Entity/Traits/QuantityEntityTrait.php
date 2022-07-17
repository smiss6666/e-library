<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait QuantityEntityTrait
{

    /**
     * @ORM\Column(name="quantity", type="integer", nullable=false, options={"default":"0"})
     */
    protected int $quantity = 0;

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

}
