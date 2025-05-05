<?php

namespace App\Controller\BackOffice;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]

class BackOfficeController extends AbstractController
{
    private FormationRepository $formationRepository;
    private CategorieRepository $categorieRepository;
    private PlaylistRepository $playlistRepository;

    public function __construct(
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository,
        PlaylistRepository $playlistRepository
    ) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }

    #[Route('/', name: 'dashboard')]
    public function index(): Response
    {
        $formationsCount = $this->formationRepository->count([]);
        $categoriesCount = $this->categorieRepository->count([]);
        $playlistsCount = $this->playlistRepository->count([]);

        $recentFormations = $this->formationRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('back_office/base.html.twig', [
            'formationsCount' => $formationsCount,
            'categoriesCount' => $categoriesCount,
            'playlistsCount' => $playlistsCount,
            'recentFormations' => $recentFormations
        ]);
    }
}