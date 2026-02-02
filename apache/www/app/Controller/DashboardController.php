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
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\FacultyService;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\TagService;

class DashboardController extends BaseController {   
    private UserService $userService;
    private CategoryService $categoryService;
    private FacultyService $facultyService;

    public function __construct(Container $container) {
        parent::__construct($container);       
        $this->userService = new UserService();
        $this->categoryService = new CategoryService();
        $this->facultyService = new FacultyService();
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

    #[Get('/categories/:categoryId/edit')]
    #[AuthMiddleware(Role::ADMIN)]
    public function editCategory(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $categoryId = (int)$pathVars['categoryId'];
        
        return $this->render("admin/edit-category", [
            'category' => $this->categoryService->getCategory($categoryId),
            "adminId" => $adminId
        ]);
    }

    #[Post('/api/edit-category')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "categoryname" => ValidationErrorCode::CATEGORY_REQUIRED,
        "categoryid" => ValidationErrorCode::CATEGORY_REQUIRED
    ])]
    public function updateCategory(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $categoryName = $request->post("categoryname");
        $categoryId = (int)$request->post("categoryid");
        
        $this->categoryService->updateCategory($categoryId, $categoryName);
        
        return Response::create()->json([
            "success" => true
        ]);
    }
    
    #[Get('/faculties')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getFaculties(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);

        $searchTerm = $request->get('search');
        $faculties = $searchTerm ? $this->facultyService->searchFaculties($searchTerm) : $this->facultyService->getAllFaculties();
        $courses = [];
        foreach ($faculties as $faculty) {
            $courses[$faculty->facultyId] = $this->courseService->getCoursesByFaculty($faculty->facultyId);
        }
        
        return $this->render("admin/faculties", [
            'faculties' => $faculties,
            'courses' => $courses,
            'adminId' => $adminId,
            'searchTerm' => $searchTerm
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

    #[Get('/faculties/:facultyId/edit')]
    #[AuthMiddleware(Role::ADMIN)]
    public function editFaculty(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        
        return $this->render("admin/edit-faculty", [
            'faculty' => $this->facultyService->getFacultyDetails($facultyId),
            "adminId" => $adminId
        ]);
    }

    #[Get('/faculties/:facultyId/edit-courses')]
    #[AuthMiddleware(Role::ADMIN)]
    public function editFacultyCourses(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        
        return $this->render("admin/change-courses", [
            'faculty' => $this->facultyService->getFacultyDetails($facultyId),
            'courses' => $this->courseService->getCoursesByFaculty($facultyId),
            "adminId" => $adminId
        ]);
    }

    
    #[Get('/api/faculties/:facultyId/courses')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getCoursesApi(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        
        $courses = $this->courseService->getCoursesByFaculty($facultyId);
        
        return Response::create()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }

    #[Post('/api/edit-faculty')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "facultyname" => ValidationErrorCode::FACULTY_REQUIRED,
        "facultyid" => ValidationErrorCode::FACULTY_REQUIRED
    ])]
    public function updateFaculty(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $facultyName = $request->post("facultyname");
        $facultyId = (int)$request->post("facultyid");
        
        $this->facultyService->updateFaculty($facultyId, $facultyName);
        
        return Response::create()->json([
            "success" => true
        ]);
    }
    
    #[Get('/faculties/:facultyId/courses/:courseId/edit')]
    #[AuthMiddleware(Role::ADMIN)]
    public function editCourse(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $courseId = (int)$pathVars['courseId'];
        
        $course = $this->courseService->getCourseDetails($courseId);
        $faculty = $this->facultyService->getFacultyDetails($facultyId);
        
        if (!$course || !$faculty) {
            return Response::create()->redirect('/faculties');
        }
        
        return $this->render('admin/edit-course', [
            'course' => $course,
            'faculty' => $faculty,
            'adminId' => $request->getAttribute(RequestAttribute::ROLE_ID)
        ]);
    }
    
    #[Post('/api/edit-course')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "coursename" => ValidationErrorCode::COURSE_REQUIRED,
        "courseid" => ValidationErrorCode::COURSE_REQUIRED,
        "facultyid" => ValidationErrorCode::FACULTY_REQUIRED
    ])]
    public function updateCourse(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $courseName = $request->post("coursename");
        $courseId = (int)$request->post("courseid");
        $facultyId = (int)$request->post("facultyid");
        
        $this->courseService->updateCourse($courseId, $courseName,$facultyId);
        
        return Response::create()->json([
            "success" => true
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
