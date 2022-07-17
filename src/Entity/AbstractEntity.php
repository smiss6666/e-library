<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contracts\EntityInterface;
use App\Entity\Traits\BaseEntityTrait;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractEntity implements EntityInterface
{
    use BaseEntityTrait;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

}
