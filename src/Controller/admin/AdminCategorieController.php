<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class AdminCategorieController extends AbstractController{

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

    #[Route('/', name: 'admin.categorie')]
    public function index(): Response
    {
        $categories = $this->categorieRepository->findAll();

        return $this->render('admin/admin.categorie.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/ajout', name: 'admin.categorie.ajout')]
    public function ajout(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();

            $this->addFlash('success', 'Catégorie créée avec succès.');
            return $this->redirectToRoute('admin.categorie');
        }

        return $this->render('admin/admin.categorie.ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'admin.categorie.delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($categorie);
            $this->entityManager->flush();
            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        }

        return $this->redirectToRoute('admin.categorie');
    }
}