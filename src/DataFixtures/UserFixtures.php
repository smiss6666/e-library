<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Contracts\UserInterface;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    protected array $admins = [
        ['username' => 'admin', 'email' => 'ad@example.com', 'firstName' => 'AdFName', 'lastName' => 'AdLName'],
    ];

    protected array $librarians = [
        ['username' => 'lib', 'email' => 'lib@example.com', 'firstName' => 'LibFName', 'lastName' => 'LibLName'],
        ['username' => 'lib2', 'email' => 'lib2@example.com', 'firstName' => 'Lib2FName', 'lastName' => 'Lib2LName'],
        ['username' => 'lib3', 'email' => 'lib3@example.com', 'firstName' => 'Lib3FName', 'lastName' => 'LibL3Name'],
    ];

    protected array $readers = [
        ['username' => 'reader', 'email' => 'reader@example.com', 'firstName' => 'RFName', 'lastName' => 'RLName'],
        ['username' => 'reader2', 'email' => 'reader2@example.com', 'firstName' => 'R2FName', 'lastName' => 'R2LName'],
        ['username' => 'reader3', 'email' => 'reader3@example.com', 'firstName' => 'R3FName', 'lastName' => 'RLName'],
    ];


    private function createEntity(string $role, array $data): User
    {
        return (new User())
            ->setUsername($data['username'])
            ->setFirstName($data['firstName'])
            ->setLastName($data['lastName'])
            ->setEmail($data['email'])
            ->setPlainPassword($data['username'])
            ->setRoles([$role])
            ->setActive(true);
    }


    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach ($this->admins as $data) {
            ++$i;
            $entity = $this->createEntity(UserInterface::ROLE_ADMIN, $data);
            $manager->persist($entity);
            $this->addReference('admin-' . $i, $entity);
        }

        $i = 0;
        foreach ($this->librarians as $data) {
            ++$i;
            $entity = $this->createEntity(UserInterface::ROLE_LIBRARIAN, $data);
            $manager->persist($entity);
            $this->addReference('librarian-' . $i, $entity);
        }

        $i = 0;
        foreach ($this->readers as $data) {
            ++$i;
            $entity = $this->createEntity(UserInterface::ROLE_READER, $data);
            $manager->persist($entity);
            $this->addReference('reader-' . $i, $entity);
        }

        // save
        $manager->flush();
    }

}
