<?php

declare(strict_types=1);

namespace App\Entity\Traits\Timestampable;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait ProlongAtEntityTrait
{

    /**
     * @ORM\Column(name="prolong_at", type="date", nullable=true)
     */
    protected ?DateTimeInterface $prolongAt = null;

    public function getProlongAt(): ?DateTimeInterface
    {
        return $this->prolongAt;
    }

    public function setProlongAt(?DateTimeInterface $prolongAt): self
    {
        $this->prolongAt = $prolongAt;

        return $this;
    }

}
