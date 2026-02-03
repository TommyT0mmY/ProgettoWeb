<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\CourseDTO;
use Unibostu\Core\exceptions\RepositoryException;
use PDO;

class UserCoursesRepository extends BaseRepository {

    /**
     * Retrieves courses for a user
     * @return CourseDTO[]
     */
    public function findCoursesByUser(string $userId): array {
        $stmt = $this->pdo->prepare(
            "SELECT c.* FROM courses c
                JOIN user_courses uc ON c.course_id = uc.course_id
                WHERE uc.user_id = :userId
                ORDER BY c.course_name"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($rows as $row) {
            $courses[] = $this->rowToDTO($row);
        }
        return $courses;
    }

    /**
     * Saves user courses
     * 
     * @param string $userId
     * @param int[] $courseIds
     * @throws RepositoryException
     */
    public function saveUserCourses(string $userId, array $courseIds): void {
        $this->executeInTransaction(function() use ($userId, $courseIds) {
            // Remove existing courses for the user
            $deleteStmt = $this->pdo->prepare(
                "DELETE FROM user_courses WHERE user_id = :userId"
            );
            $deleteStmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $deleteStmt->execute();

            // Add new courses
            $insertStmt = $this->pdo->prepare(
                "INSERT INTO user_courses (user_id, course_id) VALUES (:userId, :courseId)"
            );
            foreach ($courseIds as $courseId) {
                $insertStmt->bindValue(':userId', $userId, PDO::PARAM_STR);
                $insertStmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
                $insertStmt->execute();
            }
        });
    }

    public function subscribeUserToCourse(string $userId, int $courseId): void {
        $stmt = $this->pdo->prepare(
            "INSERT IGNORE INTO user_courses (user_id, course_id) VALUES (:userId, :courseId)"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function unsubscribeUserFromCourse(string $userId, int $courseId): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM user_courses WHERE user_id = :userId AND course_id = :courseId"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
    }

    protected function rowToDTO(array $row): CourseDTO {
        return new CourseDTO(
            (int)$row['course_id'],
            $row['course_name'],
            (int)$row['faculty_id']
        );
    }
}

