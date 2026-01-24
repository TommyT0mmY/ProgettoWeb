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
use Unibostu\Model\Service\PostService;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\DTO\UserDTO;
use Unibostu\Model\Service\FacultyService;


class UserProfileController extends BaseController {
    private $postService;
    private $courseService;
    private $userService;
    private $facultyService;
    private $categoryService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->userService = new UserService();
        $this->facultyService = new FacultyService();
        $this->categoryService = new CategoryService();
    }

    /** get user profile */
    #[Get('/user-profile')]
    public function getUserProfilePosts(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PARAMETERS);
        $postQuery = null;
        $userId = null; //per testing usare "laura.monti"

        if ($this->getAuth()->isAuthenticated(Role::ADMIN)) {
            $postQuery = PostQuery::create()
                ->forAdmin(true);                                  
        } else if ($this->getAuth()->isAuthenticated(Role::USER)) { //|| true per testing poi lo tolgo
            $userId = $this->getAuth()->getId(Role::USER);//commentare per testing
            $postQuery = PostQuery::create()
                ->forUser($userId)
                ->authoredBy($userId)
                ->inCategory($request->get('categoryId'))
                ->withTags($request->get('tags'))
                ->sortedBy($request->get('sortOrder'));
        } else {
            throw new Exception('You are not authenticated');
        }

        $user = $this->userService->getUserProfile($userId);

        return $this->render("user-profile", [
            'user' => $user,
            'posts' => $this->postService->getPosts($postQuery),
            'courses' => $this->courseService->getCoursesByUser($userId),
            'faculty' => $this->facultyService->getFacultyDetails($user->facultyId),
            'categories' => $this->categoryService->getAllCategories()
        ]);
    }
}

