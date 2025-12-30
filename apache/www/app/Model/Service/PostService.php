<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\PostRepository;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\PostWithAuthorDTO;
use Unibostu\Model\DTO\PostListDTO;
use Unibostu\Model\Entity\PostEntity;

class PostService {
    private PostRepository $postRepository;
    private UserRepository $userRepository;

    public function __construct() {
        $this->postRepository = new PostRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Carica i post per la home page con gli autori
     */
    public function loadHomePage(): PostListDTO {
        $postEntities = $this->postRepository->findAll();
        $dto = new PostListDTO();

        foreach ($postEntities as $post) {
            $author = $this->userRepository->findByEntita($post->identita);
            if ($author && !$author->utente_sospeso) {
                $dto->addPost(new PostWithAuthorDTO($post, $author));
            }
        }

        return $dto;
    }

    /**
     * Carica i post di un corso
     */
    public function loadCoursePosts(int $idcorso): array {
        return $this->postRepository->findByCourseId($idcorso);
    }

    /**
     * Ottiene tutti i post di un utente specifico con i relativi dati
     */
    public function getPostsWithAuthorByUserId(string $idutente): ?PostListDTO {
        $user = $this->userRepository->findByUserId($idutente);
        if (!$user || $user->utente_sospeso) {
            return null;
        }

        $posts = $this->postRepository->findByUserId($user->identita);
        $dto = new PostListDTO();

        foreach ($posts as $post) {
            $dto->addPost(new PostWithAuthorDTO($post, $user));
        }

        return $dto;
    }

    /**
     * Crea un nuovo post
     */
    public function createPost(
        string $titolo,
        string $descrizione,
        int $identita,
        ?string $percorso_allegato = null,
        ?int $idcorso = null
    ): int {
        $post = new PostEntity(
            0, // Sarà auto-generato dal database
            $titolo,
            $descrizione,
            $percorso_allegato,
            0,
            0,
            date('Y-m-d'),
            $identita,
            $idcorso
        );

        return $this->postRepository->save($post);
    }

    /**
     * Aggiorna un post
     */
    public function updatePost(int $idpost, string $titolo, string $descrizione): bool {
        $post = $this->postRepository->findById($idpost);
        if (!$post) {
            return false;
        }

        $post->titolo = $titolo;
        $post->descrizione = $descrizione;

        return $this->postRepository->update($post);
    }

    /**
     * Aggiunge un like a un post
     */
    public function likePost(int $idpost): bool {
        return $this->postRepository->incrementLikes($idpost);
    }

    /**
     * Aggiunge un dislike a un post
     */
    public function dislikePost(int $idpost): bool {
        return $this->postRepository->incrementDislikes($idpost);
    }

    /**
     * Elimina un post
     */
    public function deletePost(int $idpost): bool {
        return $this->postRepository->delete($idpost);
    }
}

?>