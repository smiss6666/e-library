<?php

declare(strict_types=1);

namespace App\Entity\Traits\Contact;

use Doctrine\ORM\Mapping as ORM;

trait FirstNameEntityTrait
{

    /**
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    protected ?string $firstName = null;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

}
