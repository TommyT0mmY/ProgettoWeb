<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Exception;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Container;
use Unibostu\Core\SessionManager;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Model\Service\PostService;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\UserService;

class CommunityController extends BaseController {
    
    private $postService;
    private $courseService;
    private $categoryService;
    private $userService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
        $this->userService = new UserService();
    }

    /** get Community posts */
    #[Get("/courses/:courseId")]
    public function getCommunityPosts(array $params, Request $request): Response {
        $courseId = $params['courseId'];
        $postQuery = null;
        $userId = null; //per testing usare "laura.monti"

        if ($this->getAuth()->isAuthenticatedAsAdmin()) {
            $postQuery = PostQuery::create()
                ->forAdmin(true);                                  
        } else if ($this->getAuth()->isAuthenticatedAsUser()) { //|| true per testing poi lo tolgo
            $userId = $this->getAuth()->getUserId();//commentare per testing
            $postQuery = PostQuery::create()
                ->forUser($userId)
                ->authoredBy($userId)
                ->inCourse($courseId)
                ->inCategory($request->get('categoryId'))
                ->withTags($request->get('tags'))
                ->sortedBy($request->get('sortOrder'));
        } else {
            throw new Exception('You are not authenticated');
        }

        $user = $this->userService->getUserProfile($userId);
        
        return $this->render("community", [
            "posts" => $this->postService->getPosts($postQuery),
            "courses" => $this->courseService->getCoursesByUser($userId),
            "thisCourse" => $this->courseService->getCourseDetails((int)$courseId),
            "categories" => $this->categoryService->getAllCategories(),
            "courseId" => $courseId
            ]);
    }

}

