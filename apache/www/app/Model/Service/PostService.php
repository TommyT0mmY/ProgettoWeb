<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\PostRepository;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\Repository\CourseRepository;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Model\DTO\CreatePostDTO;

class PostService {
    private PostRepository $postRepository;
    private UserRepository $userRepository;
    private CourseRepository $courseRepository;

    public function __construct() {
        $this->postRepository = new PostRepository();
        $this->userRepository = new UserRepository();
        $this->courseRepository = new CourseRepository();
    }

    public function getPosts(PostQuery $postQuery): array {
        return $this->postRepository->findWithFilters($postQuery);
    }

    /**
     * Crea un nuovo post per un utente
     * Gli utenti possono postare solo su UN UNICO corso
     * I tag devono essere dello stesso corso
     * Le categorie sono facoltative
     *
     * @throws \Exception se l'userId non è valido o il corso non appartiene all'utente
     */
    public function createPost(CreatePostDTO $dto): void {
        // Risolvi userId a utente
        $user = $this->userRepository->findByUserId($dto->userId);
        if (!$user) {
            throw new \Exception("Utente non trovato");
        }

        // Verifica che l'utente sia iscritto al corso
        $course = $this->courseRepository->findById($dto->courseId);
        if (!$course || $course->facultyId !== $user->facultyId) {
            throw new \Exception("L'utente non è iscritto a questo corso");
        }

        // Verifica che tutti i tag appartengono al corso selezionato
        foreach ($dto->tags as $tag) {
            if (!isset($tag['tagId']) || !isset($tag['courseId'])) {
                throw new \Exception("Tag non valido");
            }
            if ($tag['courseId'] !== $dto->courseId) {
                throw new \Exception("I tag devono appartenere al corso selezionato");
            }
        }

        if (empty($dto->title) || empty($dto->description)) {
            throw new \Exception("Titolo e descrizione non possono essere vuoti");
        }

        $this->postRepository->save($dto);
    }

    /**
     * Reazione (like/dislike) a un post
     * 
     * @param int $postId ID del post
     * @param string $userId ID dell'utente
     * @param string $reaction "like", "dislike" o "remove"
     */
    public function setReaction(int $postId, string $userId, string $reaction): void {
        $post = $this->postRepository->findById($postId);
        if (!$post) {
            throw new \Exception("Post non trovato");
        }
        
        if ($reaction === 'remove') {
            $this->postRepository->removeReaction($postId, $userId);
        } elseif ($reaction === 'like') {
            $this->postRepository->setReaction($postId, $userId, true);
        } elseif ($reaction === 'dislike') {
            $this->postRepository->setReaction($postId, $userId, false);
        } else {
            throw new \Exception("Reazione non valida");
        }
    }

    /**
     * Elimina un post (solo il creatore)
     * 
     * @param int $postId ID del post da eliminare
     * @param string|null $userId ID dell'utente che richiede l'eliminazione (null se admin)
     */
    public function deletePost(int $postId, ?string $userId): void {
        $post = $this->postRepository->findById($postId);
        if (!$post) {
            throw new \Exception("Post non trovato");
        }

        // Verifica che l'utente sia il creatore del post
        if ($post->userId !== $userId && $userId !== null) {
            throw new \Exception("Non hai i permessi per eliminare questo post");
        }

        $this->postRepository->delete($postId);
    }
}

