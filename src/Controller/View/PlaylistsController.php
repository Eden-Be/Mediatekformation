<?php

namespace App\Controller\View;

use App\Entity\Playlist;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController
{

    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;

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

    function __construct(PlaylistRepository  $playlistRepository,
                         CategorieRepository $categorieRepository,
                         FormationRepository $formationRespository)
    {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }

    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        $formations = $this->formationRepository->findAll();

        return $this->render("pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,
            'formations' => $formations
        ]);
    }

    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        $playlists = null;

        if ($champ === "name") {
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        }

        if ($champ === "formation") {
            $playlists = $this->playlistRepository->findAllOrderByFormationCount($ordre);
        }

        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();

        return $this->render("pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,
            'formations' => $formations
        ]);
    }

    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        $formations = $this->formationRepository->findAll();

        return $this->render("pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,
            'formations' => $formations,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);

        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }

    #[Route('/playlists', name: 'playlists.create', methods: ['POST'])]
    function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
        }

        $playlist = new Playlist();

        if (isset($data['name'])) {
            $playlist->setName($data['name']);
        }

        if (isset($data['description'])) {
            $playlist->setDescription($data['description']);
        }

        $this->playlistRepository->add($playlist);

        return new Response('Playlist créée', Response::HTTP_CREATED);
    }

    #[Route('/playlists/{id}', name: 'playlists.update', methods: ['PUT'])]
    public function update(Request $request, int $id): Response {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
        }

        $playlist = $this->playlistRepository->find($id);
        if (!$playlist) {
            return new Response('Playlist non trouvée', Response::HTTP_NOT_FOUND);
        }

        if (isset($data['name'])) {
            $playlist->setName($data['name']);
        }

        if (isset($data['description'])) {
            $playlist->setDescription($data['description']);
        }

        $this->playlistRepository->add($playlist);

        return new Response('Playlist mise à jour', Response::HTTP_OK);
    }

    #[Route('/playlists/{id}', name: 'playlists.delete', methods: ['DELETE'])]
    public function delete(int $id): Response {
        $playlist = $this->playlistRepository->find($id);

        if (!$playlist) {
            return new Response('Playlist non trouvée', Response::HTTP_NOT_FOUND);
        }

        $this->playlistRepository->remove($playlist);

        return new Response('Playlist supprimée', Response::HTTP_OK);
    }

}
