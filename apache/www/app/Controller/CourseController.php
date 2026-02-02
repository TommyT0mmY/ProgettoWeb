<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Container;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\PostService;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\TagService;
use Unibostu\Model\Service\FacultyService;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\router\middleware\ValidationMiddleware;
use Unibostu\Core\exceptions\ValidationErrorCode;

class CourseController extends BaseController {
    
    private $postService;
    private $courseService;
    private $categoryService;
    private $facultyService;
    private $tagService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
        $this->facultyService = new FacultyService();
        $this->tagService = new TagService();
    }

    #[Get("/courses/:courseId")]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function getCoursePosts(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $courseId = $params['courseId'];
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        $isAdmin = $currentRole === Role::ADMIN;
        
        $tags = [];
        $tagIds = $request->get('tags');
        if (is_array($tagIds)) {
            foreach ($tagIds as $tagId) {
                $tags[] = [
                    'tagId' => (int)$tagId,
                    'courseId' => (int)$courseId
                ];
            }
        }
        
        $postQuery = PostQuery::create()
            ->forAdmin($isAdmin)
            ->inCourse($courseId)
            ->inCategory($request->get('categoryId'))
            ->withTags($tags)
            ->sortedBy($request->get('sortOrder'));

        $viewParams = [
            "posts" => $this->postService->getPosts($postQuery),
            "thisCourse" => $this->courseService->getCourseDetails((int)$courseId),
            "categories" => $this->categoryService->getAllCategories(),
            "tags" => $this->tagService->getTagsByCourse((int)$courseId),
            "courseId" => $courseId,
            "userId" => $userId,
            "selectedCategoryId" => $request->get('categoryId'),
            "selectedSortOrder" => $request->get('sortOrder') ?? 'desc',
            "selectedTags" => $request->get('tags') ?? [],
            "isAdmin" => $isAdmin
        ];

        // Add courses only for non-admin users
        if (!$isAdmin) {
            $viewParams["courses"] = $this->courseService->getCoursesByUser($userId);
        }

        return $this->render("course", $viewParams);
    }

    #[Get('/faculties/:facultyId/courses')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getCourses(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $searchTerm = $request->get('search');

        $courses = $searchTerm ? $this->courseService->searchCoursesByNameAndFaculty($searchTerm, $facultyId) : $this->courseService->getCoursesByFaculty($facultyId);
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
        
        return $this->render('admin/edit-entity', [
            'mode' => 'edit',
            'entityType' => 'course',
            'formTitle' => 'Edit Course',
            'formId' => 'edit-course-form',
            'submitEndpoint' => '/api/edit-course',
            'backUrl' => '/faculties/' . $facultyId . '/courses',
            'fields' => [
                [
                    'name' => 'courseid',
                    'label' => 'Course ID',
                    'value' => $course->courseId,
                    'type' => 'text',
                    'readonly' => true
                ],
                [
                    'name' => 'coursename',
                    'label' => 'Course Name',
                    'value' => $course->courseName ?? '',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'facultyid',
                    'label' => 'Faculty ID',
                    'value' => $faculty->facultyId,
                    'type' => 'text',
                    'readonly' => true
                ],
                [
                    'name' => 'faculty',
                    'label' => 'Faculty',
                    'value' => $faculty->facultyName ?? '',
                    'type' => 'text',
                    'readonly' => true
                ]
            ],
            'adminId' => $request->getAttribute(RequestAttribute::ROLE_ID)
        ]);
    }
    
    #[Get('/faculties/:facultyId/courses/add')]
    #[AuthMiddleware(Role::ADMIN)]
    public function addCourse(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        
        $faculty = $this->facultyService->getFacultyDetails($facultyId);
        
        if (!$faculty) {
            return Response::create()->redirect('/faculties');
        }
        
        return $this->render('admin/edit-entity', [
            'mode' => 'add',
            'entityType' => 'course',
            'formTitle' => 'Add Course',
            'formId' => 'add-course-form',
            'submitEndpoint' => '/api/add-course',
            'backUrl' => '/faculties/' . $facultyId . '/courses',
            'fields' => [
                [
                    'name' => 'coursename',
                    'label' => 'Course Name',
                    'value' => '',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'facultyid',
                    'label' => 'Faculty ID',
                    'value' => $faculty->facultyId,
                    'type' => 'text',
                    'readonly' => true
                ],
                [
                    'name' => 'faculty',
                    'label' => 'Faculty',
                    'value' => $faculty->facultyName ?? '',
                    'type' => 'text',
                    'readonly' => true
                ]
            ],
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
        $courseName = $request->post("coursename");
        $courseId = (int)$request->post("courseid");
        $facultyId = (int)$request->post("facultyid");
        
        $this->courseService->updateCourse($courseId, $courseName, $facultyId);
        
        return Response::create()->json([
            "success" => true
        ]);
    }
    
    #[Post('/api/add-course')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "coursename" => ValidationErrorCode::COURSE_REQUIRED,
        "facultyid" => ValidationErrorCode::FACULTY_REQUIRED
    ])]
    public function createCourse(Request $request): Response {
        $courseName = $request->post("coursename");
        $facultyId = (int)$request->post("facultyid");
        
        $this->courseService->createCourse($courseName, $facultyId);
        
        return Response::create()->json([
            "success" => true
        ]);
    }

    #[Post('/api/delete-course/:facultyId/:courseId')]
    #[AuthMiddleware(Role::ADMIN)]
    public function deleteCourse(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $courseId = (int)$pathVars['courseId'];
        
        try {
            $this->courseService->deleteCourse($courseId);
            
            return Response::create()->json([
                "success" => true,
                "message" => "Course deleted successfully"
            ]);
        } catch (\Exception $e) {
            return Response::create()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}

