<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Contact\FirstNameEntityTrait;
use App\Entity\Traits\Contact\LastNameEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_filter;
use function implode;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="authors",
 * )
 */
class Author extends AbstractEntity
{

    use FirstNameEntityTrait,
        LastNameEntityTrait;

    /**
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="authors")
     * @ORM\JoinTable(name="authors_books")
     */
    protected Collection $books;

    public function __toString(): string
    {
        $labels = [];
        if (!$this->getId()) {
            $labels[] = 'New Author';
        } else {
            $labels[] = $this->getFirstName();
            $labels[] = $this->getLastName();
        }

        return implode(' ', array_filter($labels));
    }

    public function __construct()
    {
        try {
            $this->salt = bin2hex(random_bytes(12));
        } catch (\Exception $e) {
            $this->salt = '';
        }

        $this->books = new ArrayCollection();
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        $this->books->removeElement($book);

        return $this;
    }

}
