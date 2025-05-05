<?php

namespace App\Utils;

class DateFormatter
{
    /**
     * Convertit une date en format string selon le format spécifié
     *
     * @param \DateTimeInterface $date La date à formater
     * @param string $format Le format de sortie (format PHP date())
     * @return string La date formatée en chaîne de caractères
     */
    public function formatDate(\DateTimeInterface $date, string $format = 'd/m/Y H:i'): string
    {
        return $date->format($format);
    }

    /**
     * Retourne la date actuelle formatée en chaîne de caractères
     *
     * @param string $format Le format de sortie (format PHP date())
     * @return string La date actuelle formatée
     */
    public function getCurrentDateAsString(string $format = 'd/m/Y H:i'): string
    {
        return (new \DateTime())->format($format);
    }
}