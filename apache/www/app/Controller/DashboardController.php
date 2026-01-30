<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Core\router\middleware\ValidationMiddleware;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Model\Service\PostService;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\FacultyService;
use Unibostu\Model\Service\TagService;

class DashboardController extends BaseController {
    private $postService;
    private $courseService;
    private $userService;
    private $facultyService;
    private $categoryService;
    private $tagService;    
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->userService = new UserService();
        $this->facultyService = new FacultyService();
        $this->categoryService = new CategoryService();
        $this->tagService = new TagService();
    }

    /** get faculties */
    #[Get('/faculties')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getFaculties(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $user = $this->userService->getUserProfile($userId);

        $faculties = $this->facultyService->getAllFaculties();
        $courses = [];
        foreach ($faculties as $faculty) {
            $courses[$faculty->facultyId] = $this->courseService->getCoursesByFaculty($faculty->facultyId);
        }
        
        return $this->render("admin-faculties", [
            'user' => $user,
            'faculties' => $faculties,
            'courses' => $courses,
            'userId' => $userId
        ]);
    }

    /** get faculty courses */
    #[Get('/faculties/:facultyId/courses')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getCourses(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $user = $this->userService->getUserProfile($userId);

        $courses = $this->courseService->getCoursesByFaculty($facultyId);
        $tags = [];
        foreach ($courses as $course) {
            $tags[$course->courseId] = $this->tagService->getTagsByCourse($course->courseId);
        }

        return $this->render("admin-courses", [
            'user' => $user,
            'courses' => $courses,
            'faculty' => $this->facultyService->getFacultyDetails($facultyId),
            'tags' => $tags,
            'userId' => $userId
        ]);
    }

    /** get course tags */
    #[Get('/faculties/:facultyId/courses/:courseId/tags')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getTags(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $courseId = (int)$pathVars['courseId'];

        $user = $this->userService->getUserProfile($userId);

        return $this->render("admin-tags", [
            'user' => $user,
            'tags' => $this->tagService->getTagsByCourse($courseId),
            'faculty' => $this->facultyService->getFacultyDetails($facultyId),
            'userId' => $userId,
            'course' => $this->courseService->getCourseDetails($courseId)
        ]);
    }


    
}

