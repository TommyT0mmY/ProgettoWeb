<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CommentRepository;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\CommentWithAuthorDTO;
use Unibostu\Model\DTO\CommentsListDTO;
use Unibostu\Model\Entity\CommentEntity;

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
        $commentEntities = $this->commentRepository->findByPostId($postid);
        $dto = new CommentsListDTO();

        foreach ($commentEntities as $comment) {
            $author = $this->userRepository->findByEntita($comment->identita);
            if ($author && !$author->utente_sospeso) {
                $dto->addComment(new CommentWithAuthorDTO($comment, $author));
            }
        }

        return $dto;
    }

    /**
     * Crea un nuovo commento
     */
    public function createComment(
        int $idpost,
        int $idcommento,
        string $testo,
        int $identita,
        int $idpost_genitore = 0,
        int $idcommento_genitore = 0
    ): bool {
        $comment = new CommentEntity(
            $idpost,
            $idcommento,
            $testo,
            date('Y-m-d'),
            false,
            $identita,
            $idpost_genitore,
            $idcommento_genitore
        );

        return $this->commentRepository->save($comment);
    }

    /**
     * Cancella un commento
     */
    public function deleteComment(int $idpost, int $idcommento): bool {
        return $this->commentRepository->delete($idpost, $idcommento);
    }
}

?>