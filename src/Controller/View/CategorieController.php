<?php

namespace App\Controller\View;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    private CategorieRepository $repository;

    public function __construct(CategorieRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/categories', name: 'categories.create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
        }

        $categorie = new Categorie();

        if (isset($data['name'])) {
            $categorie->setName($data['name']);
        }

        $this->repository->add($categorie);

        return new Response('Catégorie créée', Response::HTTP_CREATED);
    }

    #[Route('/categories/{id}', name: 'categories.delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $categorie = $this->repository->find($id);
        if (!$categorie) {
            return new Response('Catégorie non trouvée', Response::HTTP_NOT_FOUND);
        }

        $this->repository->remove($categorie);

        return new Response('Catégorie supprimée', Response::HTTP_OK);
    }
}
