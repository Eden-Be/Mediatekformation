<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);

        // Préparation des données de test
        $this->prepareTestData();
    }

    private function prepareTestData(): void
    {
        // Purger les utilisateurs existants
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();

        // Créer quelques utilisateurs de test
        $user1 = new User();
        $user1->setEmail('test1@example.com');
        $user1->setFirstName('John');
        $user1->setLastName('Doe');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword('password');

        $user2 = new User();
        $user2->setEmail('test2@example.com');
        $user2->setFirstName('Jane');
        $user2->setLastName('Smith');
        $user2->setRoles(['ROLE_ADMIN']);
        $user2->setPassword('password');

        $this->entityManager->persist($user1);
        $this->entityManager->persist($user2);
        $this->entityManager->flush();
    }

    public function testFindByEmail(): void
    {
        $user = $this->userRepository->findOneByEmail('test1@example.com');

        $this->assertNotNull($user);
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
    }

    public function testFindByRole(): void
    {
        $admins = $this->userRepository->findByRole('ROLE_ADMIN');

        $this->assertCount(1, $admins);
        $this->assertEquals('Jane', $admins[0]->getFirstName());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Fermeture de l'EntityManager pour éviter les fuites mémoire
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
