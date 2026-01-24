<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Exception;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\DTO\UserDTO;
use Unibostu\Model\Service\FacultyService;


class StudentPreferencesController extends BaseController {
    private $courseService;
    private $userService;
    private $facultyService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->courseService = new CourseService();
        $this->userService = new UserService();
        $this->facultyService = new FacultyService();
    }
    /** get student preferences */
    #[Get('/studentpreferences')]
    public function getStudentPreferences(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);

        $userId = null; //per testing usare "laura.monti"
        
        //autenticazione,commentare per testing
        if ($this->getAuth()->isAuthenticated(Role::ADMIN)) {
            $userId = $this->getAuth()->getId(Role::USER);                                 
        } else if ($this->getAuth()->isAuthenticated(Role::USER)) { 
            $userId = $this->getAuth()->getId(Role::USER);
        } else {
            throw new Exception('You are not authenticated');
        }

        $user = $this->userService->getUserProfile($userId);

        return $this->render("studentpreferences", [
            'user' => $user,
            'courses' => $this->courseService->getCoursesByUser($userId),
            'faculty' => $this->facultyService->getFacultyDetails($user->facultyId)
        ]);
    }
}

