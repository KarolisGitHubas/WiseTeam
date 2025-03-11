<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class BookControllerTest extends WebTestCase {

    private $client;
    private $entityManager;

    protected function setUp(): void {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->clearDatabase();
    }

    private function clearDatabase(): void {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Book')->execute();
    }

    private function createAuthenticatedUser() {
        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => 'WiseTeam_test']);
        if (!$user) {
            $user = new User();
            $user->setUsername('WiseTeam_test');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        $this->client->loginUser($user);
    }

    public function testGetBooks(): void {
        $this->createAuthenticatedUser();
        $this->client->request('GET', '/api/books');

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testGetBookById(): void {
        $this->createAuthenticatedUser();

        $book = new Book();
        $book->setTitle('Test Book')
                ->setAuthor('Test Author')
                ->setIsbn('1234567890')
                ->setPublicationDate(new \DateTime('2024-01-01'))
                ->setGenre('Fiction')
                ->setNumberOfCopies(5);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/book/' . $book->getId());
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testAddBook(): void {
        $this->createAuthenticatedUser();
        for ($i = 0; $i < 10; $i++) {
            $this->client->request('POST', '/api/books/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'title' => 'New Book' . date('his'),
                'author' => 'New Author',
                'isbn' => '1234567890',
                'publicationDate' => date('Y-m-d'),
                'genre' => 'Mystery',
                'numberOfCopies' => 10
            ]));
        }

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testDeleteBook(): void {
        $this->createAuthenticatedUser();

        $book = new Book();
        $book->setTitle('Delete Me')
                ->setAuthor('Author')
                ->setIsbn('9876543210')
                ->setPublicationDate(new \DateTime('2024-01-01'))
                ->setGenre('Sci-Fi')
                ->setNumberOfCopies(3);

        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $this->client->request('DELETE', '/api/books/delete/' . $book->getId());
        $this->assertResponseStatusCodeSame(200);
    }
}
