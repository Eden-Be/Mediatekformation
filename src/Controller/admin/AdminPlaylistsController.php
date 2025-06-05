<?php


namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Description of AdminPlaylistsController
 *
 * @author edenb
 */
#[Route('/admin/playlists')]
#[IsGranted('ROLE_ADMIN')]
class AdminPlaylistsController extends AbstractController {
    
    
    private EntityManagerInterface $entityManager;
    private PlaylistRepository $playlistRepository;
    private FormationRepository $formationRepository;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        PlaylistRepository $playlistRepository,
        FormationRepository $formationRepository
    ){
        $this->entityManager = $entityManager;
        $this->playlistRepository = $playlistRepository;
        $this->formationRepository = $formationRepository;
    }
    #[Route('', name: 'admin.playlists')]
    public function index(): Response{
        $playlists = $this->playlistRepository->findAll();
        
        return $this->render('admin/admin.playlists.html.twig', [
            'playlists' => $playlists,
    ]);
    }
    #[Route('/delete/{id}', name: 'admin.playlist.delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist): Response{
        if ($this ->isCsrfTokenValid('delete'. $playlist->getId(), $request->request->get('_token'))){
            $this->entityManager->remove($playlist);
            $this->entityManager->flush();
            $this->addFlash('success', 'Playlist supprimée avec succès.');
        }
        
        return $this->redirectToRoute('admin.playlists');
    }
    #[Route('/edit/{id}', name: 'admin.playlist.edit')]
    public function edit(Request $request, Playlist $playlist): Response {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Playlist modifiée avec succès.');
            return $this->redirectToRoute('admin.playlists');
        }
        
        return $this->render('admin/admin.playlist.edit.html.twig', [
            'form'=> $form->createView(),
            'playlist' =>$playlist,
        ]);
    }
    #[Route('/ajout', name: 'admin.playlist.ajout')]
    public function ajout(Request $request): Response{
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($playlist);
            $this->entityManager->flush();
            
            $this->addflash('success', 'Playlist crée avec succès.');
            return $this->redirectToRoute('admin.playlists');
        }
        
        return $this->render('admin/admin.playlist.ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
