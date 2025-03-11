<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BooksController extends AbstractController {

    #[Route('/books', name: 'app_books')]
    public function index(): \Symfony\Component\HttpFoundation\Response {
        return $this->render('books/index.html.twig', [
                    'controller_name' => 'BooksController',
        ]);
    }

    #[Route('/api/books', name: 'api_books', methods: ['GET'])]
    public function getBooks(EntityManagerInterface $entityManager): JsonResponse {
        $books = $entityManager->getRepository(Book::class)->findAll();

        $data = [];
        foreach ($books as $book) {
            $data[] = [
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'isbn' => $book->getIsbn(),
                'publicationDate' => $book->getPublicationDate()->format('Y-m-d'),
                'genre' => $book->getGenre(),
                'numberOfCopies' => $book->getNumberOfCopies(),
                'edit' => '<a data-toggle="modal" data-target="#edit_book" data-id="' . $book->getId() . '" href="#" class="btn btn-sm btn-primary edit">Edit</a> <a data-id="' . $book->getId() . '" href="#" class="btn btn-sm btn-warning delete">Delete</a>'
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/book/{id}', name: 'api_book', methods: ['GET'])]
    public function getBookById(int $id, EntityManagerInterface $entityManager): JsonResponse {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], 404);
        }
        // Return the book details as JSON
        return new JsonResponse([
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'isbn' => $book->getIsbn(),
            'publicationDate' => $book->getPublicationDate()->format('Y-m-d'),
            'genre' => $book->getGenre(),
            'numberOfCopies' => $book->getNumberOfCopies()
        ]);
    }

    #[Route('/api/books/add', name: 'add_book', methods: ['POST'])]
    public function addBook(Request $request, EntityManagerInterface $entityManager): JsonResponse {
        // Get the raw content from the request and decode the JSON data
        $data = json_decode($request->getContent(), true);
        if (!isset($data['id'])) {
            $data['id'] = 0;
        }
        // Check if data is validvar_dump($data);
        if (!$data || !isset($data['title'], $data['author'], $data['isbn'], $data['publicationDate'], $data['genre'], $data['numberOfCopies'])) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }
        if ($data['id'] > 0) {
            // Edit an existing book
            $book = $entityManager->getRepository(Book::class)->find($data['id']);

            // Check if the book exists
            if (!$book) {
                return new JsonResponse(['error' => 'Book not found'], 404);
            }

            // Update the book details
            $book->setTitle($data['title']);
            $book->setAuthor($data['author']);
            $book->setIsbn(preg_replace('/\D/', '', $data['isbn']));
            $book->setPublicationDate(new \DateTime($data['publicationDate']));
            $book->setGenre($data['genre']);
            $book->setNumberOfCopies($data['numberOfCopies']);
        } else {
            // Create a new Book object
            $book = new Book();
            $book->setTitle($data['title']);
            $book->setAuthor($data['author']);

            $book->setIsbn(preg_replace('/\D/', '', $data['isbn']));
            $book->setPublicationDate(new \DateTime($data['publicationDate']));
            $book->setGenre($data['genre']);
            $book->setNumberOfCopies($data['numberOfCopies']);
        }
        // Persist the new book in the database
        $entityManager->persist($book);
        $entityManager->flush();

        // Return a success response
        return new JsonResponse(['message' => 'Book added successfully'], 201);
    }

    #[Route('/api/books/delete/{id}', name: 'delete_book', methods: ['DELETE'])]
    public function deleteBook(int $id, EntityManagerInterface $entityManager): JsonResponse {

        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], 404);
        }

        // Remove the book from the database
        $entityManager->remove($book);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Book deleted successfully'], 200);
    }
}
