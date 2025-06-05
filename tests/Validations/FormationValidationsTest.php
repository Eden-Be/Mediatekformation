<?php

namespace App\Tests\Validations;

use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationValidationsTest extends KernelTestCase
{
    private $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        // Symfony 5.3+ : accès au service validator
        $this->validator = self::$container->get('validator');
    }

    public function testPublishedAtDateNotInFuture()
    {
        $formation = new Formation();

        // Date dans le futur (demain)
        $futureDate = new \DateTime('tomorrow');
        $formation->setPublishedAt($futureDate);

        $errors = $this->validator->validate($formation);

        $this->assertCount(1, $errors, "Une erreur doit être levée pour une date future.");
        $this->assertSame("La date de parution ne peut pas être postérieure à aujourd'hui.", $errors[0]->getMessage());
    }

    public function testPublishedAtDateTodayOrPastIsValid()
    {
        $formation = new Formation();

        // Date aujourd’hui
        $today = new \DateTime('today');
        $formation->setPublishedAt($today);
        $errors = $this->validator->validate($formation);
        $this->assertCount(0, $errors, "Pas d'erreur pour la date d'aujourd'hui.");

        // Date dans le passé
        $pastDate = new \DateTime('yesterday');
        $formation->setPublishedAt($pastDate);
        $errors = $this->validator->validate($formation);
        $this->assertCount(0, $errors, "Pas d'erreur pour une date passée.");
    }
}
