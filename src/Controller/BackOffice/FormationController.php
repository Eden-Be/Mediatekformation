<?php

namespace App\Controller\BackOffice;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/formations', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class FormationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FormationRepository $formationRepository;
    private CategorieRepository $categorieRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository
    ) {
        $this->entityManager = $entityManager;
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/', name: 'formations_list')]
    public function list(): Response
    {
        $formations = $this->formationRepository->findAll();

        return $this->render('back_office/formations/list.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/new', name: 'formation_new')]
    public function new(Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($formation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Formation créée avec succès.');
            return $this->redirectToRoute('admin_formations_list');
        }

        return $this->render('back_office/formations/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'formation_edit')]
    public function edit(Request $request, Formation $formation): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Formation modifiée avec succès.');
            return $this->redirectToRoute('admin_formations_list');
        }

        return $this->render('back_office/formations/edit.html.twig', [
            'form' => $form->createView(),
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/delete', name: 'formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($formation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Formation supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_formations_list');
    }
}
