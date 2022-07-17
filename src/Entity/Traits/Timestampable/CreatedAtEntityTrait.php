<?php

declare(strict_types=1);

namespace App\Entity\Traits\Timestampable;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtEntityTrait
{

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $createdAt = null;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
