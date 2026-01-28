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

class CommunityController extends BaseController {
    
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

    /** get Community posts */
    #[Get("/courses/:courseId")]
    #[AuthMiddleware(Role::USER, Role::ADMIN)]
    public function getCommunityPosts(Request $request): Response {
        $params = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $courseId = $params['courseId'];
        $postQuery = null;
        $userId = null; //per testing usare "laura.monti"
        $currentRole = $request->getAttribute(RequestAttribute::ROLE);
        if ($currentRole === Role::ADMIN) {
            $postQuery = PostQuery::create()
                ->forAdmin(true);                                  
        } else if ($currentRole === Role::USER) {
            $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
            
            // Costruisce array di tags con formato corretto
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
                ->forUser($userId)
                ->authoredBy($userId)
                ->inCourse($courseId)
                ->inCategory($request->get('categoryId'))
                ->withTags($tags)
                ->sortedBy($request->get('sortOrder'));
        }

        $user = $this->userService->getUserProfile($userId);

        return $this->render("community", [
            "posts" => $this->postService->getPosts($postQuery),
            "courses" => $this->courseService->getCoursesByUser($userId),
            "thisCourse" => $this->courseService->getCourseDetails((int)$courseId),
            "categories" => $this->categoryService->getAllCategories(),
            "tags" => $this->tagService->getTagsByCourse((int)$courseId),
            "courseId" => $courseId,
            "userId" => $userId,
            "selectedCategoryId" => $request->get('categoryId'),
            "selectedSortOrder" => $request->get('sortOrder') ?? 'desc',
            "selectedTags" => $request->get('tags') ?? [],
            ]);
    }

}

