<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture implements DependentFixtureInterface
{

    protected array $books = [
        ['quantity' => 1, 'title' => 'Title-1', 'description' => 'Description-1', 'authors' => [1]],
        ['quantity' => 2, 'title' => 'Title-2', 'description' => 'Description-2', 'authors' => [1,2]],
        ['quantity' => 3, 'title' => 'Title-3', 'description' => 'Description-3', 'authors' => [2]],
        ['quantity' => 4, 'title' => 'Title-4', 'description' => 'Description-4', 'authors' => [3,4]],
        ['quantity' => 5, 'title' => 'Title-5', 'description' => 'Description-5', 'authors' => [5]],
        ['quantity' => 6, 'title' => 'Title-6', 'description' => 'Description-6', 'authors' => [1]],
        ['quantity' => 7, 'title' => 'Title-7', 'description' => 'Description-7', 'authors' => [1,2]],
        ['quantity' => 8, 'title' => 'Title-8', 'description' => 'Description-8', 'authors' => [1,3]],
        ['quantity' => 9, 'title' => 'Title-9', 'description' => 'Description-9', 'authors' => [1,4]],
        ['quantity' => 10, 'title' => 'Title-10', 'description' => 'Description-10', 'authors' => [1,4]],
        ['quantity' => 11, 'title' => 'Title-11', 'description' => 'Description-11', 'authors' => [2,2]],
        ['quantity' => 12, 'title' => 'Title-12', 'description' => 'Description-12', 'authors' => [2,2]],
        ['quantity' => 13, 'title' => 'Title-13', 'description' => 'Description-13', 'authors' => [2,3]],
        ['quantity' => 14, 'title' => 'Title-14', 'description' => 'Description-14', 'authors' => [2,4]],
        ['quantity' => 15, 'title' => 'Title-15', 'description' => 'Description-15', 'authors' => [5]],
    ];

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            AuthorFixtures::class,
        ];
    }

    private function createEntity(array $data): Book
    {
        $entity = (new Book())
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setQuantity($data['quantity']);

        foreach ($data['authors'] as $key) {
            $reference = 'author-' . $key;
            $author = $this->hasReference($reference) ? $this->getReference($reference) : null;
            $entity->addAuthor($author);
        }

        return $entity;
    }

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach ($this->books as $data) {
            ++$i;
            $entity = $this->createEntity($data);
            $manager->persist($entity);
            $this->addReference('book-' . $i, $entity);
        }

        // save
        $manager->flush();
    }


}
