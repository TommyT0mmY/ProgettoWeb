<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CommentRepository;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\CommentWithAuthorDTO;
use Unibostu\Model\DTO\CommentsListDTO;
use Unibostu\Model\DTO\CreateCommentDTO;

class CommentService {
    private CommentRepository $commentRepository;
    private UserRepository $userRepository;

    public function __construct() {
        $this->commentRepository = new CommentRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Ottiene tutti i commenti di un post con gli autori
     */
    public function getCommentsByPostId(int $postid): CommentsListDTO {
        $comments = $this->commentRepository->findByPostId($postid);
        $commetsList = array();

        foreach ($comments as $comment) {
            $author = $this->userRepository->findByUserIdPublic($comment->idutente);
            if ($author && !$author->utente_sospeso) {
                $commetsList[] = new CommentWithAuthorDTO($comment, $author);
            }
        }

        return new CommentsListDTO($commetsList);
    }

    /**
     * Crea un nuovo commento
     * @throws \Exception se l'idutente non è valido o non esiste
     */
    public function createComment(CreateCommentDTO $dto): void {
        // Verifica che l'utente esista
        $user = $this->userRepository->findByUserIdPublic($dto->idutente);
        if (!$user) {
            throw new \Exception("Utente non trovato");
        }

        if ($user->utente_sospeso) {
            throw new \Exception("Utente sospeso, non può creare commenti");
        }

        if (empty($dto->testo)) {
            throw new \Exception("Il testo del commento non può essere vuoto");
        }

        $this->commentRepository->save($dto);
    }

    /**
     * Cancella un commento
     * @throws \Exception se l'idutente non esiste o non è proprietario del commento
     */
    public function deleteComment(int $idcommento, int $idpost, string $idutente): void {
        $comment = $this->commentRepository->findById($idcommento, $idpost);
        if (!$comment) {
            throw new \Exception("Commento non trovato");
        }

        if ($comment->idutente !== $idutente) {
            throw new \Exception("Non sei il proprietario di questo commento");
        }

        $this->commentRepository->delete($idcommento, $idpost);
    }
}