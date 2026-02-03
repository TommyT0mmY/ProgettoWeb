<?php
declare(strict_types=1);

namespace Unibostu\Core\router\middleware;

use Attribute;
use RuntimeException;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\Http\RequestHandlerInterface;
use Unibostu\Core\Http\Response;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\Role;

/**
 * Enforces authentication for specified roles.
 *
 * Injects ROLE and ROLE_ID RequestAttributes into the request.
 * 
 * @see RequestAttribute::ROLE
 * @see RequestAttribute::ROLE_ID
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class AuthMiddleware extends AbstractMiddleware {
    /** @var Role[] */
    private array $roles;

    public function __construct(Role ...$roles) {
        $this->roles = $roles;
    }

    /**
     * Processes authentication for the request.
     *
     * @param Request $request Incoming request.
     * @param RequestHandlerInterface $handler Next handler.
     * @return Response HTTP response.
     * @throws RuntimeException With code 401 if unauthorized.
     */
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
            throw new RuntimeException("Unauthorized", 401);
        }
        $request = $request->withAttribute(RequestAttribute::ROLE, $currentRole)->withAttribute(RequestAttribute::ROLE_ID, $auth->getId($currentRole));
        return $handler->handle($request);
    }
}
