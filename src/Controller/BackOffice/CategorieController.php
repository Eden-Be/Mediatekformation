<?php

namespace App\Controller\BackOffice;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class CategorieController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CategorieRepository $categorieRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategorieRepository    $categorieRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/', name: 'categories_list')]
    public function list(): Response
    {
        $categories = $this->categorieRepository->findAll();

        return $this->render('back_office/categories/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'category_new')]
    public function new(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();

            $this->addFlash('success', 'Catégorie créée avec succès.');
            return $this->redirectToRoute('admin_categories_list');
        }

        return $this->render('back_office/categories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($categorie);
            $this->entityManager->flush();
            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_categories_list');
    }
}
