<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{

    protected $authors = [
        ['firstName' => 'Author-1-first-name', 'lastName' => 'LastName-1'],
        ['firstName' => 'Author-2-first-name', 'lastName' => 'LastName-2'],
        ['firstName' => 'Author-3-first-name', 'lastName' => 'LastName-3'],
        ['firstName' => 'Author-4-first-name', 'lastName' => 'LastName-4'],
        ['firstName' => 'Author-5-first-name', 'lastName' => 'LastName-5'],
    ];

    private function createEntity(array $data): Author
    {
        return (new Author())
            ->setFirstName($data['firstName'] ?? null)
            ->setLastName($data['lastName'] ?? null);
    }

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach ($this->authors as $data) {
            ++$i;
            $entity = $this->createEntity($data);
            $manager->persist($entity);
            $this->addReference('author-' . $i, $entity);
        }

        // save
        $manager->flush();
    }

}
