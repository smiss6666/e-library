<?php

declare(strict_types=1);

namespace App\Entity\Traits\Timestampable;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait StartAtEntityTrait
{

    /**
     * @ORM\Column(name="start_at", type="date", nullable=true)
     */
    protected ?DateTimeInterface $startAt = null;

    public function getStartAt(): ?DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(?DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

}
