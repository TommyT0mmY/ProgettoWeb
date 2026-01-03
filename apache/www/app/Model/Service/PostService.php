<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\PostRepository;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\Repository\CourseRepository;
use Unibostu\Model\DTO\PostWithAuthorDTO;
use Unibostu\Model\DTO\PostListDTO;
use Unibostu\Model\DTO\PostFilterDTO;
use Unibostu\Model\DTO\CreateUserPostDTO;

class PostService {
    private PostRepository $postRepository;
    private UserRepository $userRepository;
    private CourseRepository $courseRepository;

    public function __construct() {
        $this->postRepository = new PostRepository();
        $this->userRepository = new UserRepository();
        $this->courseRepository = new CourseRepository();
    }

    /**
     * Carica i post della homepage con filtri
     * Se nessun filtro è fornito, carica tutti i post
     * Filtri disponibili: corsi, categorie, ordinamento
     */
    public function loadPostsWithFilters(PostFilterDTO $filter): PostListDTO {
        $postDtos = $this->postRepository->findWithFilters($filter);
        $postsList = array();

        foreach ($postDtos as $post) {
            $author = $this->userRepository->findByUserId($post->idutente);
            
            if ($author && !$author->utente_sospeso) {
                $postsList[] = new PostWithAuthorDTO($post, $author);
            }
        }

        return new PostListDTO($postsList);
    }

    /**
     * Ottiene tutti i post di un utente specifico con i relativi dati
     */
    public function getPostsWithAuthorByUserId(string $idutente): ?PostListDTO {
        $user = $this->userRepository->findByUserId($idutente);
        if (!$user || $user->utente_sospeso) {
            return null;
        }

        $postDtos = $this->postRepository->findByUserId($idutente);
        $postsList = array();

        foreach ($postDtos as $post) {
            $postsList[] = new PostWithAuthorDTO($post, $user);
        }

        return new PostListDTO($postsList);
    }

    /**
     * Crea un nuovo post per un utente
     * Gli utenti possono postare solo su UN UNICO corso
     * I tag devono essere dello stesso corso
     * Le categorie sono facoltative
     *
     * @throws \Exception se l'idutente non è valido o il corso non appartiene all'utente
     */
    public function createUserPost(CreateUserPostDTO $dto): void {
        // Risolvi idutente a utente
        $user = $this->userRepository->findByUserId($dto->idutente);
        if (!$user) {
            throw new \Exception("Utente non trovato");
        }

        // Verifica che l'utente sia iscritto al corso
        $course = $this->courseRepository->findById($dto->idcorso);
        if (!$course || $course->idfacolta !== $user->idfacolta) {
            throw new \Exception("L'utente non è iscritto a questo corso");
        }

        // Verifica che tutti i tag appartengono al corso selezionato
        foreach ($dto->tags as $tag) {
            if (!isset($tag['tipo']) || !isset($tag['idcorso'])) {
                throw new \Exception("Tag non valido");
            }
            if ($tag['idcorso'] !== $dto->idcorso) {
                throw new \Exception("I tag devono appartenere al corso selezionato");
            }
        }

        if (empty($dto->titolo) || empty($dto->descrizione)) {
            throw new \Exception("Titolo e descrizione non possono essere vuoti");
        }

        $this->postRepository->save($dto);
    }

    /**
     * Aggiunge un voto (like/dislike) a un post
     * 
     * @param int $idpost ID del post
     * @param string $idutente ID dell'utente
     * @param bool $isLike true per like, false per dislike
     */
    public function votePost(int $idpost, string $idutente, bool $isLike): void {
        $post = $this->postRepository->findById($idpost);
        if (!$post) {
            throw new \Exception("Post non trovato");
        }
        
        $this->postRepository->addVote($idpost, $idutente, $isLike);
    }

    /**
     * Rimuove un voto (like/dislike) a un post
     * 
     * @param int $idpost ID del post
     * @param string $idutente ID dell'utente
     */
    public function removeVote(int $idpost, string $idutente): void {
        $post = $this->postRepository->findById($idpost);
        if (!$post) {
            throw new \Exception("Post non trovato");
        }
        
        $this->postRepository->removeVote($idpost, $idutente);
    }

    /**
     * Elimina un post (solo il creatore)
     * 
     * @param int $idpost ID del post da eliminare
     * @param string $idutente ID dell'utente che richiede l'eliminazione
     */
    public function deletePost(int $idpost, string $idutente): void {
        $post = $this->postRepository->findById($idpost);
        if (!$post) {
            throw new \Exception("Post non trovato");
        }

        // Verifica che l'utente sia il creatore del post
        if ($post->idutente !== $idutente) {
            throw new \Exception("Non hai i permessi per eliminare questo post");
        }

        $this->postRepository->delete($idpost);
    }
}

