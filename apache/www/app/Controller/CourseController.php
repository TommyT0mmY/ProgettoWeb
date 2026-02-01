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
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\TagService;

class CourseController extends BaseController {
    
    private $postService;
    private $courseService;
    private $categoryService;
    private $userService;
    private $tagService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->categoryService = new CategoryService();
        $this->userService = new UserService();
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

}

