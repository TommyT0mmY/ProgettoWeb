<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Core\router\middleware\ValidationMiddleware;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CategoryService;

class CategoryController extends BaseController {
    private CategoryService $categoryService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->categoryService = new CategoryService();
    }

    #[Get('/categories')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getCategories(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
       
        return $this->render("admin/categories", [
            'categories' => $this->categoryService->getAllCategories(),
            'adminId' => $adminId 
        ]);
    }

    #[Get('/categories/:categoryId/edit')]
    #[AuthMiddleware(Role::ADMIN)]
    public function editCategory(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $pathVars = $request->getAttribute(RequestAttribute::PATH_VARIABLES);
        $categoryId = (int)$pathVars['categoryId'];
        $category = $this->categoryService->getCategory($categoryId);
        
        return $this->render("admin/edit-entity", [
            'mode' => 'edit',
            'entityType' => 'category',
            'formTitle' => 'Edit Category',
            'formId' => 'edit-category-form',
            'submitEndpoint' => '/api/edit-category',
            'backUrl' => '/categories',
            'fields' => [
                [
                    'name' => 'categoryid',
                    'label' => 'Category ID',
                    'value' => $category->categoryId,
                    'type' => 'text',
                    'readonly' => true
                ],
                [
                    'name' => 'categoryname',
                    'label' => 'Category Name',
                    'value' => $category->categoryName ?? '',
                    'type' => 'text',
                    'required' => true
                ]
            ],
            "adminId" => $adminId
        ]);
    }
    
    #[Get('/categories/add')]
    #[AuthMiddleware(Role::ADMIN)]
    public function addCategory(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        
        return $this->render("admin/edit-entity", [
            'mode' => 'add',
            'entityType' => 'category',
            'formTitle' => 'Add Category',
            'formId' => 'add-category-form',
            'submitEndpoint' => '/api/add-category',
            'backUrl' => '/categories',
            'fields' => [
                [
                    'name' => 'categoryname',
                    'label' => 'Category Name',
                    'value' => '',
                    'type' => 'text',
                    'required' => true
                ]
            ],
            "adminId" => $adminId
        ]);
    }

    #[Post('/api/edit-category')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "categoryname" => ValidationErrorCode::CATEGORY_REQUIRED,
        "categoryid" => ValidationErrorCode::CATEGORY_REQUIRED
    ])]
    public function updateCategory(Request $request): Response {
        $categoryName = $request->post("categoryname");
        $categoryId = (int)$request->post("categoryid");
        
        $this->categoryService->updateCategory($categoryId, $categoryName);
        
        return Response::create()->json([
            "success" => true
        ]);
    }
    
    #[Post('/api/add-category')]
    #[AuthMiddleware(Role::ADMIN)]
    #[ValidationMiddleware([
        "categoryname" => ValidationErrorCode::CATEGORY_REQUIRED
    ])]
    public function createCategory(Request $request): Response {
        $categoryName = $request->post("categoryname");
        
        $this->categoryService->createCategory($categoryName);
        
        return Response::create()->json([
            "success" => true
        ]);
    }
}
