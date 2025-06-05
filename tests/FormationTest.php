<?php

namespace App\Tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    public function testGetPublishedAtStringReturnsFormattedDate()
    {
        $formation = new Formation();

        $date = new \DateTime('2025-06-05');
        $formation->setPublishedAt($date);

        $this->assertEquals('05/06/2025', $formation->getPublishedAtString());
    }

    public function testGetPublishedAtStringReturnsEmptyWhenDateIsNull()
    {
        $formation = new Formation();

        $formation->setPublishedAt(null);

        $this->assertEquals('', $formation->getPublishedAtString());
    }
}
