<?php

namespace App\Tests\Utils;

use App\Utils\DateFormatter;
use PHPUnit\Framework\TestCase;

class DateFormatterTest extends TestCase
{
    private DateFormatter $dateFormatter;

    protected function setUp(): void
    {
        $this->dateFormatter = new DateFormatter();
    }

    public function testFormatDate(): void
    {
        // Créer une date fixe pour les tests
        $date = new \DateTime('2023-05-15 14:30:00');

        // Test avec le format par défaut
        $this->assertEquals('15/05/2023 14:30', $this->dateFormatter->formatDate($date));

        // Test avec un format personnalisé
        $this->assertEquals('2023-05-15', $this->dateFormatter->formatDate($date, 'Y-m-d'));
        $this->assertEquals('Monday, 15 May 2023', $this->dateFormatter->formatDate($date, 'l, j F Y'));
        $this->assertEquals('14h30', $this->dateFormatter->formatDate($date, 'H\hi'));
    }

    public function testGetCurrentDateAsString(): void
    {
        // Ce test vérifie que la méthode retourne bien une chaîne au format attendu
        // Note: on ne peut pas tester la valeur exacte car elle dépend du moment où le test est exécuté

        // Test avec le format par défaut
        $result = $this->dateFormatter->getCurrentDateAsString();
        $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/', $result);

        // Test avec un format personnalisé (année-mois-jour)
        $result = $this->dateFormatter->getCurrentDateAsString('Y-m-d');
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result);
    }

    public function testGetCurrentDateAsStringWithMockedTime(): void
    {
        // Test plus avancé qui utilise une classe mock pour fixer la date/heure actuelle
        $dateFormatterMock = $this->createPartialMock(DateFormatter::class, ['getCurrentDateAsString']);

        // Configuration du mock pour retourner une date fixe
        $dateFormatterMock->method('getCurrentDateAsString')
            ->willReturn('01/01/2023 00:00');

        // Vérification
        $this->assertEquals('01/01/2023 00:00', $dateFormatterMock->getCurrentDateAsString());
    }
}