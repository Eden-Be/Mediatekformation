<?php

namespace App\Controller\BackOffice;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/playlists', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class PlaylistController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PlaylistRepository $playlistRepository;
    private FormationRepository $formationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlaylistRepository $playlistRepository,
        FormationRepository $formationRepository
    ) {
        $this->entityManager = $entityManager;
        $this->playlistRepository = $playlistRepository;
        $this->formationRepository = $formationRepository;
    }

    #[Route('/', name: 'playlists_list')]
    public function list(): Response
    {
        $playlists = $this->playlistRepository->findAll();

        return $this->render('back_office/playlists/list.html.twig', [
            'playlists' => $playlists,
        ]);
    }

    #[Route('/new', name: 'playlist_new')]
    public function new(Request $request): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($playlist);
            $this->entityManager->flush();

            $this->addFlash('success', 'Playlist créée avec succès.');
            return $this->redirectToRoute('admin_playlists_list');
        }

        return $this->render('back_office/playlists/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'playlist_edit')]
    public function edit(Request $request, Playlist $playlist): Response
    {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Playlist modifiée avec succès.');
            return $this->redirectToRoute('admin_playlists_list');
        }

        return $this->render('back_office/playlists/edit.html.twig', [
            'form' => $form->createView(),
            'playlist' => $playlist,
        ]);
    }

    #[Route('/{id}/delete', name: 'playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist): Response
    {
        if ($this->isCsrfTokenValid('delete'.$playlist->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($playlist);
            $this->entityManager->flush();
            $this->addFlash('success', 'Playlist supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_playlists_list');
    }
}
