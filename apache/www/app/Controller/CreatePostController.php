<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Exception;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\TagService;
use Unibostu\Model\Service\UserService;

class CreatePostController extends BaseController {
    private $courseService;
    private $categoryService;
    private $tagService;
    private $userService; 

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
        $this->tagService = new TagService();
        $this->userService = new UserService();
    }

    #[Get('/courses/:courseId/createpost')]
    #[AuthMiddleware(Role::USER)]
    public function createPosts(Request $request): Response {
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $courseId = $pathVars['courseId'];

        $userId = $request->getAttribute(RequestAttribute::ROLE_ID); 
        $user = $this->userService->getUserProfile($userId);
        
        return $this->render("createpost", [
            "courses" => $this->courseService->getCoursesByUser($userId),
            "thisCourse" => $this->courseService->getCourseDetails((int)$courseId),
            "categories" => $this->categoryService->getAllCategories(),
            "tags" => $this->tagService->getTagsByCourse((int)$courseId)             
        ]);
    }       
}

