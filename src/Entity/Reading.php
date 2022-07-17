<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\QuantityEntityTrait;
use App\Entity\Traits\Timestampable\EndAtEntityTrait;
use App\Entity\Traits\Timestampable\ProlongAtEntityTrait;
use App\Entity\Traits\Timestampable\StartAtEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="reading"
 * )
 */
class Reading extends AbstractEntity
{

    use QuantityEntityTrait,
        StartAtEntityTrait,
        EndAtEntityTrait,
        ProlongAtEntityTrait;

    public const READING_TYPE_SUBSCRIPTION = 1;
    public const READING_TYPE_READING_ROOM = 2;

    public const READING_TYPES = [
        self::READING_TYPE_SUBSCRIPTION => 'Subscription',
        self::READING_TYPE_READING_ROOM => 'Reading Hall',
    ];

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected ?int $readingType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="reading")
     * @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     */
    protected ?Book $book;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reading")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user;

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReadingType(): ?int
    {
        return $this->readingType;
    }

    public function setReadingType(int $readingType): self
    {
        $this->readingType = $readingType;

        return $this;
    }

}
