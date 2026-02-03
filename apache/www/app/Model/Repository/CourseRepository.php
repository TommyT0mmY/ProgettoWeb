<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\CourseDTO;
use Unibostu\Core\exceptions\RepositoryException;
use PDO;

class CourseRepository extends BaseRepository {

    public function exists(int $courseId): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM courses WHERE course_id = :courseId"
        );
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $count = (int)$stmt->fetchColumn();

        return $count > 0;
    }

    /**
     * Retrieves a course by ID
     *
     * @throws RepositoryException if query fails
     */
    public function findById(int $courseId): ?CourseDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM courses WHERE course_id = :courseId"
        );
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Retrieves all courses
     *
     * @throws RepositoryException if query fails
     */
    public function findAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM courses ORDER BY course_name"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Retrieves courses by faculty
     *
     * @throws RepositoryException if query fails
     */
    public function findByFaculty(int $facultyId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM courses WHERE faculty_id = :facultyId ORDER BY course_name"
        );
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Searches courses by name within a specific faculty.
     */
    public function searchByNameAndFaculty(string $searchTerm, int $facultyId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE faculty_id = :facultyId AND course_name LIKE :searchTerm ORDER BY course_name");
        
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Gets the courses of a faculty and an user
     */
    public function findByFacultyAndUser(int $facultyId, string $userId): array {
        $stmt = $this->pdo->prepare(
            "SELECT c.* FROM courses c
             JOIN user_courses uc ON c.course_id = uc.course_id
             WHERE c.faculty_id = :facultyId AND uc.user_id = :userId
             ORDER BY c.course_name"
        );
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Saves a new course
     *
     * @throws RepositoryException if save fails
     */
    public function save(string $courseName, int $facultyId): void {
        $this->executeInTransaction(function() use ($courseName, $facultyId) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO courses (course_name, faculty_id)
                 VALUES (:courseName, :facultyId)"
            );
            $stmt->bindValue(':courseName', $courseName, PDO::PARAM_STR);
            $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to save course");
            }
        });
    }

    /**
     * Updates course data
     *
     * @throws RepositoryException if update fails
     */
    public function update(int $courseId, string $courseName, int $facultyId): void {
        $this->executeInTransaction(function() use ($courseId, $courseName, $facultyId) {
            $stmt = $this->pdo->prepare(
                "UPDATE courses 
                 SET course_name = :courseName, faculty_id = :facultyId
                 WHERE course_id = :courseId"
            );
            $stmt->bindValue(':courseName', $courseName, PDO::PARAM_STR);
            $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
            $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to update course");
            }
        });
    }

    /**
     * Deletes a course
     *
     * @throws RepositoryException if deletion fails
     */
    public function delete(int $courseId): void {
        $this->executeInTransaction(function() use ($courseId) {
            $stmt = $this->pdo->prepare(
                "DELETE FROM courses WHERE course_id = :courseId"
            );
            $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to delete course");
            }
        });
    }

    /**
     * Checks if a user is enrolled in a course
     */
    public function isUserEnrolled(string $userId, int $courseId): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM user_courses WHERE user_id = :userId AND course_id = :courseId"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    protected function rowToDTO(array $row): CourseDTO {
        return new CourseDTO(
            (int)$row['course_id'],
            $row['course_name'],
            (int)$row['faculty_id']
        );
    }
}
