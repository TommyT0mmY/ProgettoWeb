<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Exception;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\security\Auth;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\TagService;
use Unibostu\Model\Service\UserService;

class CreatePostController extends BaseController {
    private $courseService;
    private $categoryService;
    private $tagService;
    private $userService; 

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
        $this->tagService = new TagService();
        $this->userService = new UserService();
    }
    #[Get('/courses/:courseId/createpost')]
    public function createPosts(array $params, Request $request): Response {
        $courseId = $params['courseId'];

        $userId = null; //per testing usare "laura.monti"
        
        //autenticazione,commentare per testing
        if ($this->getAuth()->isAuthenticatedAsAdmin()) {
            $userId = $this->getAuth()->getAdminId();                                 
        } else if ($this->getAuth()->isAuthenticatedAsUser()) { 
            $userId = $this->getAuth()->getUserId();
        } else {
            throw new Exception('You are not authenticated');
        }

        $user = $this->userService->getUserProfile($userId);
        
        return $this->render("createpost", [
            "courses" => $this->courseService->getCoursesByUser($userId),
            "thisCourse" => $this->courseService->getCourseDetails((int)$courseId),
            "categories" => $this->categoryService->getAllCategories(),
            "tags" => $this->tagService->getTagsByCourse((int)$courseId)             
        ]);
    }       
}

