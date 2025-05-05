# Documentation Technique - MediaTekFormation

## Table des matières
1. [Introduction](#introduction)
2. [Architecture](#architecture)
3. [Structure du Projet](#structure-du-projet)
4. [Controllers](#controllers)
5. [Modèles](#modèles)
6. [Vues](#vues)
7. [Services](#services)
8. [Configuration](#configuration)
9. [Tests](#tests)

## Introduction

MediaTekFormation est une application Symfony 6.4.7 conçue pour gérer et présenter des formations en ligne. L'application permet de consulter des formations regroupées par catégories et playlists, avec une interface d'administration (backoffice) pour leur gestion.

L'application utilise une base de données MySQL et s'appuie sur les composants standards de Symfony ainsi que sur Doctrine ORM pour la persistance des données.

### Versions et Environnement
- PHP: 8.1
- Symfony: 6.4.7
- Base de données: MySQL
- Moteur de templates: Twig

## Architecture

L'application suit le pattern architectural MVC (Modèle-Vue-Contrôleur) standard de Symfony:

- **Modèles**: Entités Doctrine représentant les tables de la base de données (formations, catégories, playlists)
- **Vues**: Templates Twig pour le rendu HTML
- **Contrôleurs**: Classes PHP gérant les requêtes HTTP et retournant des réponses


## Controllers

### AccueilController
Gère l'affichage de la page d'accueil du site.

```php
/**
 * @Route("/", name="accueil")
 */
public function index(): Response
{
    // Logique pour la page d'accueil
    // Probablement récupération des formations récentes ou mises en avant
    return $this->render('accueil/base.html.twig', [
        // données pour le template
    ]);
}
```

### BackOfficeController
Gère les fonctionnalités d'administration du site.

```php
/**
 * @Route("/admin", name="admin")
 * @IsGranted("ROLE_ADMIN")
 */
public function index(): Response
{
    // Interface d'administration pour gérer les formations, catégories et playlists
    return $this->render('backoffice/base.html.twig', [
        // données pour le template
    ]);
}
```

### CategorieController
Gère l'affichage et la manipulation des catégories de formations.

```php
/**
 * @Route("/categories", name="categories")
 */
public function index(): Response
{
    // Liste des catégories
    return $this->render('categories/base.html.twig', [
        // données pour le template
    ]);
}

/**
 * @Route("/categories/{id}", name="categorie_show")
 */
public function show($id): Response
{
    // Affichage des formations d'une catégorie spécifique
    return $this->render('categories/show.html.twig', [
        // données pour le template
    ]);
}
```

### FormationsController
Gère l'affichage et la manipulation des formations.

```php
/**
 * @Route("/formations", name="formations")
 */
public function index(): Response
{
    // Liste des formations
    return $this->render('formations/base.html.twig', [
        // données pour le template
    ]);
}

/**
 * @Route("/formations/{id}", name="formation_show")
 */
public function show($id): Response
{
    // Affichage des détails d'une formation spécifique
    return $this->render('formations/show.html.twig', [
        // données pour le template
    ]);
}
```

## Modèles

### Formation
Représente une formation disponible sur la plateforme.

```php
/**
 * @ORM\Entity(repositoryClass=FormationRepository::class)
 */
class Formation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="formations")
     */
    private $categorie;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, mappedBy="formations")
     */
    private $playlists;

    // Getters et setters
}
```

### Categorie
Représente une catégorie de formations.

```php
/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Formation::class, mappedBy="categorie")
     */
    private $formations;

    // Getters et setters
}
```

### Playlist
Représente une playlist regroupant plusieurs formations.

```php
/**
 * @ORM\Entity(repositoryClass=PlaylistRepository::class)
 */
class Playlist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Formation::class, inversedBy="playlists")
     */
    private $formations;

    // Getters et setters
}
```

## Vues

Les vues sont organisées par fonctionnalités et utilisent le moteur de templates Twig.

### Layout Principal
```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}MediaTekFormation{% endblock %}</title>
        {% block stylesheets %}{% endblock %}
    </head>
    <body>
        <header>
            {# Navigation principale #}
        </header>
        <main>
            {% block body %}{% endblock %}
        </main>
        <footer>
            {# Pied de page #}
        </footer>
        {% block javascripts %}{% endblock %}
    </body>
</html>
```

### Page d'Accueil
```twig
{# templates/accueil/index.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}Accueil - {{ parent() }}{% endblock %}

{% block body %}
    <section class="featured-formations">
        {# Formations mises en avant #}
    </section>
    
    <section class="categories-overview">
        {# Aperçu des catégories #}
    </section>
    
    <section class="recent-formations">
        {# Formations récentes #}
    </section>
{% endblock %}
```

### Liste des Formations
```twig
{# templates/formations/index.html.twig #}
{% extends 'base.html.twig' %}

{% block title %}Formations - {{ parent() }}{% endblock %}

{% block body %}
    <h1>Toutes nos formations</h1>
    
    <div class="filters">
        {# Filtres pour les formations #}
    </div>
    
    <div class="formations-list">
        {% for formation in formations %}
            <div class="formation-card">
                <h3>{{ formation.title }}</h3>
                <p>{{ formation.description|slice(0, 100) }}...</p>
                <a href="{{ path('formation_show', {'id': formation.id}) }}">Voir détails</a>
            </div>
        {% endfor %}
    </div>
    
    {# Pagination #}
{% endblock %}
```

## Services

### FormationService
Gère la logique métier liée aux formations.

```php
/**
 * Service pour la gestion des formations
 */
class FormationService
{
    private $formationRepository;
    
    public function __construct(FormationRepository $formationRepository)
    {
        $this->formationRepository = $formationRepository;
    }
    
    /**
     * Recherche des formations par critères
     */
    public function searchFormations(array $criteria): array
    {
        return $this->formationRepository->findBySearchCriteria($criteria);
    }
    
    /**
     * Récupère les formations les plus récentes
     */
    public function getRecentFormations(int $limit = 5): array
    {
        return $this->formationRepository->findByOrderByDateDesc($limit);
    }
    
    /**
     * Récupère les formations d'une catégorie
     */
    public function getFormationsByCategory(int $categoryId): array
    {
        return $this->formationRepository->findByCategorie($categoryId);
    }
}
```

### CategorieService
Gère la logique métier liée aux catégories.

```php
/**
 * Service pour la gestion des catégories
 */
class CategorieService
{
    private $categorieRepository;
    
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Récupère toutes les catégories avec le nombre de formations associées
     */
    public function getAllCategoriesWithFormationCount(): array
    {
        return $this->categorieRepository->findAllWithFormationCount();
    }
}
```

### PlaylistService
Gère la logique métier liée aux playlists.

```php
/**
 * Service pour la gestion des playlists
 */
class PlaylistService
{
    private $playlistRepository;
    
    public function __construct(PlaylistRepository $playlistRepository)
    {
        $this->playlistRepository = $playlistRepository;
    }
    
    /**
     * Récupère les playlists populaires
     */
    public function getPopularPlaylists(int $limit = 5): array
    {
        // Logique pour déterminer les playlists populaires
        return $this->playlistRepository->findPopular($limit);
    }
}
```

## Configuration

### Routes
Configuration des routes principales de l'application.

```yaml
# config/routes.yaml
controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute
```

### Services
Configuration des services principaux de l'application.

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false

    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'
```

### Base de données
Configuration de la connexion à la base de données.

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        server_version: '8.0'
```

## Tests

### Tests Fonctionnels
Tests vérifiant le comportement des contrôleurs et leur intégration.

```php
// tests/Functional/Controller/HomeControllerTest.php
namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur MediaTekFormation');
    }
}
```

### Tests Unitaires
Tests vérifiant le comportement des services et autres composants isolés.

```php
// tests/Unit/Service/FormationServiceTest.php
namespace App\Tests\Unit\Service;

use App\Repository\FormationRepository;
use App\Service\FormationService;
use PHPUnit\Framework\TestCase;

class FormationServiceTest extends TestCase
{
    public function testGetRecentFormations(): void
    {
        // Création d'un mock pour le repository
        $repositoryMock = $this->createMock(FormationRepository::class);
        $repositoryMock->expects($this->once())
            ->method('findByOrderByDateDesc')
            ->with(5)
            ->willReturn(['formation1', 'formation2']);
            
        $service = new FormationService($repositoryMock);
        $result = $service->getRecentFormations(5);
        
        $this->assertCount(2, $result);
    }
}
```