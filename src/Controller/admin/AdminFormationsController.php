<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/formations')]
#[IsGranted('ROLE_ADMIN')]
class AdminFormationsController extends AbstractController
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    
    /**
     * 
     * @param type $formationRepository
     * @param type $CategorieRepository
     */
    public function __construct( EntityManagerInterface $entityManager, FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->entityManager = $entityManager;
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    #[Route('', name: 'admin.formations')]
    public function index(): Response {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/admin.formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    #[Route('/admin/suppr/{id}', name: 'admin.formation.suppr')]
    public function suppr(int$id): Response{
        $formation = $this->formationRepository->find($id);
        $this->formationRepository->remove($formation);
        return $this->redirectToRoute('admin.formations');
    }
    #[Route('/admin/edit/{id}', name : 'admin.formation.edit')]
    public function edit(Request $request, Formation $formation): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Formation modifiée avec succès.');
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render('admin/admin.formation.edit.html.twig', [
            'form' => $form->createView(),
            'formation' => $formation,
        ]);
    }
    #[Route('/ajout', name : 'admin.formation.ajout')]
    public function ajout(Request $request): Response {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($formation);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Formation crée avec succès.');
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/admin.formation.ajout.html.twig", [
            'formation' => $formation,
            'form' => $form->createView()
        ]);
    }

   
}