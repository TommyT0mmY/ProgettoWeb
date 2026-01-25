<?php
declare(strict_types=1);

namespace Unibostu\Core\router\middleware;

use Attribute;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\Http\RequestHandlerInterface;
use Unibostu\Core\Http\Response;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\Role;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class AuthMiddleware extends AbstractMiddleware {
    /** @var Role[] */
    private array $roles;

    public function __construct(Role ...$roles) {
        $this->roles = $roles;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        /** @var Auth  */
        $auth = $this->container->get(Auth::class);
        $isAuthorized = in_array(Role::GUEST, $this->roles, true);
        $currentRole = Role::GUEST;
        foreach ($this->roles as $role) {
            $roleAccepted = $auth->isAuthenticated($role);
            if ($roleAccepted) {
                $currentRole = $role;
            }
            $isAuthorized = $isAuthorized || $roleAccepted;
        }
        if (!$isAuthorized) {
            return new Response('Unauthorized', 401);
        }
        $request = $request->withAttribute(RequestAttribute::ROLE, $currentRole)->withAttribute(RequestAttribute::ROLE_ID, $auth->getId($currentRole));
        return $handler->handle($request);
    }
}
