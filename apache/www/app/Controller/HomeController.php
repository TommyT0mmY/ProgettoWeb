<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Model\Service\PostService;

class HomeController extends BaseController {
    private $postService;
    
    #[Get('/')]
    public function index(): Response {
        return $this->render("home", []);
    }

    #[Get('/homepage')]
    public function getHomepagePosts(array $params, Request $request): Response {
        $postQuery = PostQuery::create()->forAdmin(true);
/*             ->forUser("alice.testa")
            ->inCategory($request->get('categoryId'))
            ->sortedBy($request->get('sortOrder'))
            ->afterPost((int)$request->get('lastPostId'))
            ->withLimit((int)$request->get('limit'));
 */
        $this->postService = new PostService();
        return $this->render("home", ["posts" => $this->postService->getPosts($postQuery)]);
    }
}

