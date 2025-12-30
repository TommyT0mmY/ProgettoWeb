<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Model\Service\CommentService;

class CommentsController {
    private CommentService $commentService;

    public function __construct() {
        $this->commentService = new CommentService();
    }

    /**
     * Carica e visualizza i commenti di un post
     */
    public function viewComments(int $postid): void {
        $commentsDTO = $this->commentService->getCommentsByPostId($postid);
        // Passa i dati alla view
        // $this->view('comments/list', ['commentsDTO' => $commentsDTO]);
    }

    /**
     * Crea un nuovo commento
     */
    public function create(int $postid, int $idcommento, string $testo, int $identita): void {
        $result = $this->commentService->createComment($postid, $idcommento, $testo, $identita);
        // Gestisci il risultato
    }

    /**
     * Elimina un commento
     */
    public function delete(int $idpost, int $idcommento): void {
        $result = $this->commentService->deleteComment($idpost, $idcommento);
        // Gestisci il risultato
    }
}

?>