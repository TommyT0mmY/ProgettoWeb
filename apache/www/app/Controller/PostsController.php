<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Model\Service\PostService;

class PostsController {
    private PostService $postService;

    public function __construct() {
        $this->postService = new PostService();
    }

    /**
     * Carica la home page con i post più recenti
     */
    public function loadHomePage(): void {
        $homePageDTO = $this->postService->loadHomePage();
        // Passa i dati alla view
        // $this->view('home/index', ['homePageDTO' => $homePageDTO]);
    }

    /**
     * Carica i post di un utente specifico
     */
    public function loadUserPosts(string $idutente): void {
        $userPostsDTO = $this->postService->getPostsWithAuthorByUserId($idutente);
        // Passa i dati alla view
        // $this->view('user/posts', ['userPostsDTO' => $userPostsDTO]);
    }

    /**
     * Carica i post di un corso
     */
    public function loadCoursePosts(int $idcorso): void {
        $posts = $this->postService->loadCoursePosts($idcorso);
        // Passa i dati alla view
        // $this->view('course/posts', ['posts' => $posts]);
    }

    /**
     * Crea un nuovo post
     */
    public function create(string $titolo, string $descrizione, string $idutente, ?string $percorso_allegato = null, ?int $idcorso = null): void {
        $idpost = $this->postService->createPost($titolo, $descrizione, $idutente, $percorso_allegato, $idcorso);
        // Gestisci il risultato (redirect o messaggio)
    }

    /**
     * Aggiunge un like a un post
     */
    public function like(int $idpost): void {
        $this->postService->likePost($idpost);
    }

    /**
     * Aggiunge un dislike a un post
     */
    public function dislike(int $idpost): void {
        $this->postService->dislikePost($idpost);
    }

    /**
     * Elimina un post
     */
    public function delete(int $idpost, string $idutente): void {
        $result = $this->postService->deletePost($idpost, $idutente);
        // Gestisci il risultato
    }
}

?>
