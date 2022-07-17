<?php

declare(strict_types=1);

namespace App\Entity\Traits\Contact;

use Doctrine\ORM\Mapping as ORM;

trait LastNameEntityTrait
{

    /**
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected ?string $lastName = null;

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

}
