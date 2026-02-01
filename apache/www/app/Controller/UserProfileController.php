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
use Unibostu\Core\router\routes\Put;
use Unibostu\Model\Service\PostService;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\FacultyService;

class UserProfileController extends BaseController {
    private $postService;
    private $courseService;
    private $userService;
    private $facultyService;
    private $categoryService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->userService = new UserService();
        $this->facultyService = new FacultyService();
        $this->categoryService = new CategoryService();
    }

    #[Get('/users/:userid')]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function getUserProfilePosts(Request $request): Response {
        $viewedUserId = $request->getAttribute(RequestAttribute::PATH_VARIABLES)['userid'];
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        $isAdmin = $currentRole === Role::ADMIN;

        $postQuery = PostQuery::create()
            ->forAdmin($isAdmin)
            ->authoredBy($viewedUserId)
            ->inCategory($request->get('categoryId'))
            ->withTags($request->get('tags'))
            ->sortedBy($request->get('sortOrder'));
        $user = $this->userService->getUserProfile($userId);
        $viewedUser = $this->userService->getUserProfile($viewedUserId);

        $viewParams = [
            'user' => $user,
            'viewedUser' => $viewedUser,
            'posts' => $this->postService->getPosts($postQuery),
            'faculty' => $this->facultyService->getFacultyDetails($viewedUser->facultyId),
            'categories' => $this->categoryService->getAllCategories(),
            'userId' => $userId,
            'sortOrder' => $postQuery->getSortOrder(),
            'categoryId' => $postQuery->getCategory(),
            'tags' => $postQuery->getTags(),
            "selectedCategoryId" => $request->get('categoryId'),
            "selectedSortOrder" => $request->get('sortOrder') ?? 'desc',
            'isAdmin' => $isAdmin
        ];

        // Add courses only for non-admin users
        if (!$isAdmin) {
            $viewParams['courses'] = $this->courseService->getCoursesByUser($userId);
        }

        return $this->render("user-profile", $viewParams);
    }

    #[Get('/select-courses')]
    #[AuthMiddleware(Role::USER)]
    public function index(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        return $this->render("select-courses", [
            "faculties" => $this->facultyService->getAllFaculties(),
            "userFacultyId" => $this->userService->getUserProfile($userId)->facultyId,
            "userId" => $userId
        ]);
    }

    #[Get('/api/select-courses/faculty/:facultyId')]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware(validateCsrf: false)]
    public function getList(Request $request): Response {
        $facultyId = (int)($request->getAttribute(RequestAttribute::PATH_VARIABLES)['facultyId']);
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        if (!$this->facultyService->facultyExists($facultyId)) {
            ValidationException::build()->addError(ValidationErrorCode::FACULTY_INVALID)->throwIfAny();
        }
        $courses = $this->courseService->getCoursesByFaculty($facultyId);
        $subscribedCourses = $this->courseService->getCoursesByFacultyAndUser($facultyId, $userId);
        return Response::create()->json([
            "success" => true,
            "courses" => $courses,
            "subscribedCourses" => $subscribedCourses
        ]);
    }

    /**
     * Post requests must contain:
     * - subscribeTo: array of course IDs to subscribe to
     * - unsubscribeFrom: array of course IDs to unsubscribe from
     * - a valid csrf pair 
     */
    #[Post('/api/select-courses')]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware()]
    public function applyCourseSelection(Request $request): Response {
        $subscribeTo = $request->post("subscribeTo", []);
        $unsubscribeFrom = $request->post("unsubscribeFrom", []);
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        foreach (array_merge($subscribeTo, $unsubscribeFrom) as $courseId) { // Validating every course id
            if (!$this->courseService->courseExists((int)$courseId)) {
                ValidationException::build()->addError(ValidationErrorCode::COURSE_INVALID)->throwIfAny();
            }
        }
        // Apply subscriptions
        $this->courseService->subscribeUserToCourses($userId, array_map('intval', $subscribeTo));
        $this->courseService->unsubscribeUserFromCourses($userId, array_map('intval', $unsubscribeFrom));
        return Response::create()->json([
            "success" => true
        ]);
    }

    #[Get('/edit-profile')]
    #[AuthMiddleware(Role::USER)]
    public function editProfilePage(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $user = $this->userService->getUserProfile($userId);
        
        return $this->render("edit-profile", [
            "user" => $user,
            "faculties" => $this->facultyService->getAllFaculties(),
            "userId" => $userId
        ]);
    }

    #[Post('/api/edit-profile')]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware([
        "firstname" => ValidationErrorCode::FIRSTNAME_REQUIRED,
        "lastname" => ValidationErrorCode::LASTNAME_REQUIRED,
        "facultyid" => ValidationErrorCode::FACULTY_REQUIRED,
    ])]
    public function updateProfile(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $firstName = $request->post("firstname");
        $lastName = $request->post("lastname");
        $facultyId = (int)$request->post("facultyid");
        
        $this->userService->updateBasicProfile($userId, $firstName, $lastName, $facultyId);
        
        return Response::create()->json([
            "success" => true
        ]);
    }

    #[Get('/change-password')]
    #[AuthMiddleware(Role::USER)]
    public function changePasswordPage(Request $request): Response {
        return $this->render("change-password", []);
    }

    #[Post('/api/change-password')]
    #[AuthMiddleware(Role::USER)]
    #[ValidationMiddleware([
        "currentpassword" => ValidationErrorCode::PASSWORD_REQUIRED,
        "newpassword" => ValidationErrorCode::PASSWORD_REQUIRED,
    ])]
    public function updatePassword(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $currentPassword = $request->post("currentpassword");
        $newPassword = $request->post("newpassword");
        
        $this->userService->updatePassword($userId, $currentPassword, $newPassword);
        
        return Response::create()->json([
            "success" => true
        ]);
    }

    #[Put('/api/users/:userid/suspension')]
    #[AuthMiddleware(Role::ADMIN)]
    public function toggleUserBan(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::PATH_VARIABLES)['userid'];
        $action = $request->post('action');
        
        if (!$this->userService->exists($userId)) {
            ValidationException::build()->addError(ValidationErrorCode::USER_NOT_FOUND)->throwIfAny();
        }
        
        if ($action === 'ban') {
            $this->userService->suspendUser($userId);
        } elseif ($action === 'unban') {
            $this->userService->unsuspendUser($userId);
        } else {
            ValidationException::build()->addError(ValidationErrorCode::INVALID_REQUEST)->throwIfAny();
        }
        
        return Response::create()->json([
            'success' => true,
        ]);
    }
}

