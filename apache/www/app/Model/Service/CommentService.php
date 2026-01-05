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
    public function getCommentsByPostId(int $postId): array {
        return $this->commentRepository->findByPostId($postId);
    }

    /**
     * Crea un nuovo commento
     * @throws \Exception se l'userId non è valido o non esiste
     */
    public function createComment(CreateCommentDTO $dto): void {
        // Verifica che l'utente esista
        $user = $this->userRepository->findByUserId($dto->userId);
        if (!$user) {
            throw new \Exception("Utente non trovato");
        }

        if ($user->suspended) {
            throw new \Exception("Utente sospeso, non può creare commenti");
        }

        if (empty($dto->text)) {
            throw new \Exception("Il testo del commento non può essere vuoto");
        }

        $this->commentRepository->save($dto);
    }

    /**
     * Cancella un commento
     * @throws \Exception se l'userId non esiste o non è proprietario del commento
     */
    public function deleteComment(int $commentId, int $postId, string $userId): void {
        $comment = $this->commentRepository->findById($commentId, $postId);
        if (!$comment) {
            throw new \Exception("Commento non trovato");
        }

        if ($comment->author->userId !== $userId) {
            throw new \Exception("Non sei il proprietario di questo commento");
        }

        $this->commentRepository->delete($commentId, $postId);
    }
}