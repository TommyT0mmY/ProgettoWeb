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
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\FacultyService;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\TagService;

class DashboardController extends BaseController {   
    private $userService;
    private $categoryService;
    private $facultyService;
    private $courseService;
    private $tagService; 

    public function __construct(Container $container) {
        parent::__construct($container);       
        $this->userService = new UserService();
        $this->categoryService = new CategoryService();
        $this->facultyService = new FacultyService();
        $this->courseService = new CourseService();
        $this->tagService = new TagService();
    }

    
    #[Get('/dashboard')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getDashboard(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        
        return $this->render("admin/dashboard", [
        'users' => $this->userService->getAllUsers(),        
        'categories' => $this->categoryService->getAllCategories(),
        'faculties' => $this->facultyService->getAllFaculties(),
        'adminId' => $adminId
        ]);
    }

     
    #[Get('/users')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getUsers(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);

        $users = $this->userService->getAllUsers();
        $faculties = [];
        foreach ($users as $user) {
            $faculties[$user->userId] = $this->facultyService->getFacultyDetails($user->facultyId);
        }
        
        return $this->render("admin/users", [
            'users' => $users,
            'adminId' => $adminId,
            'faculties' => $faculties
        ]);
    }

    
    #[Get('/categories')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getCategories(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
       
        return $this->render("admin/categories", [
            'categories' => $this->categoryService->getAllCategories(),
            'adminId' => $adminId 
        ]);
    }

    
    #[Get('/faculties')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getFaculties(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);

        $faculties = $this->facultyService->getAllFaculties();
        $courses = [];
        foreach ($faculties as $faculty) {
            $courses[$faculty->facultyId] = $this->courseService->getCoursesByFaculty($faculty->facultyId);
        }
        
        return $this->render("admin/faculties", [
            'faculties' => $faculties,
            'courses' => $courses,
            'adminId' => $adminId
        ]);
    }

    
    #[Get('/faculties/:facultyId/courses')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getCourses(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];

        $courses = $this->courseService->getCoursesByFaculty($facultyId);
        $tags = [];
        foreach ($courses as $course) {
            $tags[$course->courseId] = $this->tagService->getTagsByCourse($course->courseId);
        }

        return $this->render("admin/courses", [
            'courses' => $courses,
            'faculty' => $this->facultyService->getFacultyDetails($facultyId),
            'tags' => $tags,
            'adminId' => $adminId
        ]);
    }

    
    #[Get('/faculties/:facultyId/courses/:courseId/tags')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getTags(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $courseId = (int)$pathVars['courseId'];


        return $this->render("admin/tags", [
            'tags' => $this->tagService->getTagsByCourse($courseId),
            'faculty' => $this->facultyService->getFacultyDetails($facultyId),
            'adminId' => $adminId,
            'course' => $this->courseService->getCourseDetails($courseId)
        ]);
    }
    
}
