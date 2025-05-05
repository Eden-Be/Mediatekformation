<?php

namespace App\Controller\View;

use App\Entity\Formation;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController
{

    /**
     *
     * @var FormationRepository
     */
    private FormationRepository $formationRepository;

    /**
     *
     * @var CategorieRepository
     */
    private CategorieRepository $categorieRepository;

    /**
     *
     * @var PlaylistRepository
     */
    private PlaylistRepository $playlistRepository;

    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }

    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table = ""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }

    #[Route('/formations', name: 'formations.create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
        }

        $formation = new Formation();

        $formation
            ->setPublishedAt(new \DateTime())
            ->setTitle($data['title'] ?? null)
            ->setDescription($data['description'] ?? null)
            ->setVideoId($data['videoId'] ?? null);

        if (!empty($data['playlist_id'])) {
            $playlist = $this->playlistRepository->find($data['playlist_id']);
            if ($playlist) {
                $formation->setPlaylist($playlist);
            } else {
                return new Response('Playlist non trouvée', Response::HTTP_BAD_REQUEST);
            }
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $catId) {
                $category = $this->categorieRepository->find($catId);
                if ($category) {
                    $formation->addCategory($category);
                }
            }
        }

        $this->formationRepository->add($formation);

        return new Response('Formation créée', Response::HTTP_CREATED);
    }

    #[Route('/formations/{id}', name: 'formations.update', methods: ['PUT'])]
    public function update(Request $request, int $id): Response {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new Response('Données invalides', Response::HTTP_BAD_REQUEST);
        }

        $formation = $this->formationRepository->find($id);

        if (!$formation) {
            return new Response('Formation non trouvée', Response::HTTP_NOT_FOUND);
        }

        if (isset($data['title'])) {
            $formation->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $formation->setDescription($data['description']);
        }

        if (isset($data['videoId'])) {
            $formation->setVideoId($data['videoId']);
        }

        if (isset($data['playlist_id'])) {
            $playlist = $this->playlistRepository->find($data['playlist_id']);
            if (!$playlist) {
                return new Response('Playlist non trouvée', Response::HTTP_BAD_REQUEST);
            }
            $formation->setPlaylist($playlist);
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($formation->getCategories() as $cat) {
                $formation->removeCategory($cat);
            }

            foreach ($data['categories'] as $catId) {
                $category = $this->categorieRepository->find($catId);
                if ($category) {
                    $formation->addCategory($category);
                }
            }
        }

        $this->formationRepository->add($formation);

        return new Response('Formation mise à jour', Response::HTTP_OK);
    }

    #[Route('/formations/{id}', name: 'formations.delete', methods: ['DELETE'])]
    public function delete(int $id): Response {
        $formation = $this->formationRepository->find($id);

        if (!$formation) {
            return new Response('Formation non trouvée', Response::HTTP_NOT_FOUND);
        }

        $this->formationRepository->remove($formation);

        return new Response('Formation supprimée', Response::HTTP_OK);
    }

}
