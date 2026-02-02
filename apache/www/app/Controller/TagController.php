<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Core\router\middleware\ValidationMiddleware;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\TagService;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\FacultyService;

class TagController extends BaseController {
    private TagService $tagService;
    private CourseService $courseService;
    private FacultyService $facultyService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->tagService = new TagService();
        $this->courseService = new CourseService();
        $this->facultyService = new FacultyService();
    }

    #[Get('/faculties/:facultyId/courses/:courseId/tags')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getTags(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $courseId = (int)$pathVars['courseId'];

        $searchTerm = $request->get('search');
        $tags = $searchTerm ? $this->tagService->searchTags($searchTerm, $courseId) : $this->tagService->getTagsByCourse($courseId);

        return $this->render("admin/tags", [
            'tags' => $tags,
            'faculty' => $this->facultyService->getFacultyDetails($facultyId),
            'adminId' => $adminId,
            'course' => $this->courseService->getCourseDetails($courseId)
        ]);
    }
    
    #[Get('/faculties/:facultyId/courses/:courseId/tags/:tagId/edit')]
    #[AuthMiddleware(Role::ADMIN)]
    public function editTag(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $courseId = (int)$pathVars['courseId'];
        $tagId = (int)$pathVars['tagId'];
        
        $tags = $this->tagService->getTagsByCourse($courseId);
        $tag = null;
        foreach ($tags as $t) {
            if ($t->tagId === $tagId) {
                $tag = $t;
                break;
            }
        }
        
        if (!$tag) {
            return Response::create()->redirect('/faculties/' . $facultyId . '/courses/' . $courseId . '/tags');
        }
        
        $course = $this->courseService->getCourseDetails($courseId);
        
        return $this->render('admin/edit-entity', [
            'mode' => 'edit',
            'entityType' => 'tag',
            'formTitle' => 'Edit Tag',
            'formId' => 'edit-tag-form',
            'submitEndpoint' => '/api/edit-tag',
            'backUrl' => '/faculties/' . $facultyId . '/courses/' . $courseId . '/tags',
            'fields' => [
                [
                    'name' => 'tagid',
                    'label' => 'Tag ID',
                    'value' => $tag->tagId,
                    'type' => 'text',
                    'readonly' => true
                ],
                [
                    'name' => 'tagname',
                    'label' => 'Tag Name',
                    'value' => $tag->tag_name ?? '',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'courseid',
                    'label' => 'Course ID',
                    'value' => $courseId,
                    'type' => 'text',
                    'readonly' => true
                ],
                [
                    'name' => 'coursename',
                    'label' => 'Course',
                    'value' => $course->courseName ?? '',
                    'type' => 'text',
                    'readonly' => true
                ]
            ],
            'adminId' => $request->getAttribute(RequestAttribute::ROLE_ID)
        ]);
    }
    
    #[Get('/faculties/:facultyId/courses/:courseId/tags/add')]
    #[AuthMiddleware(Role::ADMIN)]
    public function addTag(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $courseId = (int)$pathVars['courseId'];
        
        $course = $this->courseService->getCourseDetails($courseId);
        
        if (!$course) {
            return Response::create()->redirect('/faculties/' . $facultyId . '/courses');
        }
        
        return $this->render('admin/edit-entity', [
            'mode' => 'add',
            'entityType' => 'tag',
            'formTitle' => 'Add Tag',
            'formId' => 'add-tag-form',
            'submitEndpoint' => '/api/add-tag',
            'backUrl' => '/faculties/' . $facultyId . '/courses/' . $courseId . '/tags',
            'fields' => [
                [
                    'name' => 'tagname',
                    'label' => 'Tag Name',
                    'value' => '',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'courseid',
                    'label' => 'Course ID',
                    'value' => $courseId,
                    'type' => 'text',
                    'readonly' => true
                ],
                [
                    'name' => 'coursename',
                    'label' => 'Course',
                    'value' => $course->courseName ?? '',
                    'type' => 'text',
                    'readonly' => true
                ]
            ],
            'adminId' => $request->getAttribute(RequestAttribute::ROLE_ID)
        ]);
    }
    
    #[Post('/api/edit-tag')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "tagname" => ValidationErrorCode::TAG_REQUIRED,
        "tagid" => ValidationErrorCode::TAG_REQUIRED,
        "courseid" => ValidationErrorCode::COURSE_REQUIRED
    ])]
    public function updateTag(Request $request): Response {
        $tagName = $request->post("tagname");
        $tagId = (int)$request->post("tagid");
        $courseId = (int)$request->post("courseid");
        
        $this->tagService->updateTag($tagId, $tagName, $courseId);
        
        return Response::create()->json([
            "success" => true
        ]);
    }
    
    #[Post('/api/add-tag')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "tagname" => ValidationErrorCode::TAG_REQUIRED,
        "courseid" => ValidationErrorCode::COURSE_REQUIRED
    ])]
    public function createTag(Request $request): Response {
        $tagName = $request->post("tagname");
        $courseId = (int)$request->post("courseid");
        
        $this->tagService->createTag($tagName, $courseId);
        
        return Response::create()->json([
            "success" => true
        ]);
    }

    #[Post('/api/delete-tag/:facultyId/:courseId/:tagId')]
    #[AuthMiddleware(Role::ADMIN)]
    public function deleteTag(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $tagId = (int)$pathVars['tagId'];
        $courseId = (int)$pathVars['courseId'];
        
        try {
            $this->tagService->deleteTag($tagId, $courseId);
            
            return Response::create()->json([
                "success" => true,
                "message" => "Tag deleted successfully"
            ]);
        } catch (\Exception $e) {
            return Response::create()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
