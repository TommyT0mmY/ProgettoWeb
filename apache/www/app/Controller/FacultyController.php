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
use Unibostu\Core\router\routes\Put;
use Unibostu\Core\router\routes\Delete;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\FacultyService;
use Unibostu\Model\Service\CourseService;

class FacultyController extends BaseController {
    private FacultyService $facultyService;
    private CourseService $courseService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->facultyService = new FacultyService();
        $this->courseService = new CourseService();
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
            'adminId' => $adminId
        ]);
    }

    #[Get('/faculties/:facultyId/edit')]
    #[AuthMiddleware(Role::ADMIN)]
    public function editFaculty(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $faculty = $this->facultyService->getFacultyDetails($facultyId);
        
        return $this->render("admin/edit-entity", [
            'mode' => 'edit',
            'entityType' => 'faculty',
            'formTitle' => 'Edit Faculty',
            'formId' => 'edit-faculty-form',
            'submitEndpoint' => '/api/faculties/' . $facultyId,
            'backUrl' => '/faculties',
            'fields' => [
                [
                    'name' => 'facultyid',
                    'label' => 'Faculty ID',
                    'value' => $faculty->facultyId,
                    'type' => 'text',
                    'readonly' => true
                ],
                [
                    'name' => 'facultyname',
                    'label' => 'Faculty Name',
                    'value' => $faculty->facultyName ?? '',
                    'type' => 'text',
                    'required' => true
                ]
            ],
            "adminId" => $adminId
        ]);
    }
    
    #[Get('/faculties/add')]
    #[AuthMiddleware(Role::ADMIN)]
    public function addFaculty(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        
        return $this->render("admin/edit-entity", [
            'mode' => 'add',
            'entityType' => 'faculty',
            'formTitle' => 'Add Faculty',
            'formId' => 'add-faculty-form',
            'submitEndpoint' => '/api/faculties',
            'backUrl' => '/faculties',
            'fields' => [
                [
                    'name' => 'facultyname',
                    'label' => 'Faculty Name',
                    'value' => '',
                    'type' => 'text',
                    'required' => true
                ]
            ],
            "adminId" => $adminId
        ]);
    }

    #[Put('/api/faculties/:facultyId')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "facultyname" => ValidationErrorCode::FACULTY_REQUIRED
    ])]
    public function updateFaculty(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        $facultyName = $request->post("facultyname");
        
        $this->facultyService->updateFaculty($facultyId, $facultyName);
        
        return Response::create()->json([
            "success" => true
        ]);
    }
    
    #[Post('/api/faculties')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "facultyname" => ValidationErrorCode::FACULTY_REQUIRED
    ])]
    public function createFaculty(Request $request): Response {
        $facultyName = $request->post("facultyname");
        
        $this->facultyService->createFaculty($facultyName);
        
        return Response::create()->json([
            "success" => true
        ]);
    }

    #[Delete('/api/faculties/:facultyId')]
    #[AuthMiddleware(Role::ADMIN)]
    public function deleteFaculty(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $facultyId = (int)$pathVars['facultyId'];
        
        $this->facultyService->deleteFaculty($facultyId);
        
        return Response::create()->json([
            "success" => true
        ]);
    }
}
