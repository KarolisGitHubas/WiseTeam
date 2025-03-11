<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookFake extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $book = new Book();
            $book->setTitle($faker->sentence(3));
            $book->setAuthor($faker->name);
            $book->setIsbn($faker->isbn13);
            $book->setPublicationDate($faker->dateTimeBetween('-10 years', 'now'));
            $book->setGenre($faker->randomElement(['Horror', 'Sci-Fi', 'Fantasy', 'Romance', 'Thriller']));
            $book->setNumberOfCopies($faker->numberBetween(1, 50));

            $manager->persist($book);
        }

        $manager->flush();
    }
}
