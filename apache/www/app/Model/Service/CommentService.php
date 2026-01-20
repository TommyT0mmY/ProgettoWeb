<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CommentRepository;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\CreateCommentDTO;
use Unibostu\Model\DTO\CommentDTO;

class CommentService {
    private CommentRepository $commentRepository;
    private UserRepository $userRepository;

    public function __construct() {
        $this->commentRepository = new CommentRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Ottiene tutti i commenti di un post con gli autori
     * @return CommentWithAuthorDTO[] Array di commenti con autori
     */
    public function getCommentsByPostId(int $postId): array {
        return $this->commentRepository->findByPostId($postId);
    }

    /**
     * Crea un nuovo commento
     * @throws \Exception se l'userId non è valido o non esiste
     * @throws \Exception se il testo del commento è vuoto
     * @throws \Exception se l'utente è sospeso
     * @return CommentWithAuthorDTO Il commento creato con l'autore
     */
    public function createComment(CreateCommentDTO $dto): CommentDTO {
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

        return $this->commentRepository->save($dto);
    }

    /**
     * Cancella un commento
     * @throws \Exception se l'userId non esiste o non è proprietario del commento
     * @throws \Exception se il commento non esiste
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