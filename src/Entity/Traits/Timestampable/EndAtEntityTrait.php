<?php

declare(strict_types=1);

namespace App\Entity\Traits\Timestampable;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait EndAtEntityTrait
{

    /**
     * @ORM\Column(name="end_at", type="date", nullable=true)
     */
    protected ?DateTimeInterface $endAt = null;

    public function getEndAt(): ?DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

}
