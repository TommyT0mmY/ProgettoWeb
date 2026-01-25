<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Exception;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\FacultyService;


class StudentPreferencesController extends BaseController {
    private $courseService;
    private $userService;
    private $facultyService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->courseService = new CourseService();
        $this->userService = new UserService();
        $this->facultyService = new FacultyService();
    }

    #[Get('/studentpreferences')]
    #[AuthMiddleware(Role::USER)]
    public function getStudentPreferences(Request $request): Response {
        //autenticazione,commentare per testing
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $user = $this->userService->getUserProfile($userId);

        return $this->render("studentpreferences", [
            'user' => $user,
            'courses' => $this->courseService->getCoursesByUser($userId),
            'faculty' => $this->facultyService->getFacultyDetails($user->facultyId)
        ]);
    }

    #[Get('/select-courses')]
    #[AuthMiddleware(Role::USER)]
    public function getSelectCourses(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        return $this->render("select-courses", [
            'subscribedCourses' => $this->courseService->getCoursesByUser($userId)
        ]);
    }
}

