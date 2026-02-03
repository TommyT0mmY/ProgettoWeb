<?php
declare(strict_types=1);

namespace Unibostu\Core\security;
use Unibostu\Core\SessionManager;
use Unibostu\Model\Service\RoleService;
use Unibostu\Model\Service\AdminService;
use Unibostu\Model\Service\UserService;

readonly class RoleData {
    public function __construct(
        public string $idKey,
        public RoleService $service,
    ) {}
}

/**
 * Handles authentication for USER and ADMIN roles.
 * 
 * @see Role for role definitions.
 */
class Auth {
    private array $roleData = [];

    public function __construct(
        private SessionManager $sessionManager
    ) {
        $this->roleData = [
            Role::USER->name => new RoleData(
                "user_id",
                new UserService(),
            ),
            Role::ADMIN->name => new RoleData(
                "admin_id",
                new AdminService(),
            ),
        ];
    }

    /**
     * Authenticates a user and starts a session.
     *
     * Clears any existing session data and regenerates session ID on success.
     *
     * @param Role $role Role to authenticate as.
     * @param string $id User/admin identifier.
     * @param string $password Plain text password.
     * @return bool True if authenticated.
     */
    public function login(Role $role, string $id, string $password): bool {
        $data = $this->roleData[$role->name] ?? null;
        if ($data === null) return false;
        [ $idKey, $service ] = [ $data->idKey, $data->service ];
        $authenticated = $service->checkCredentials($id, $password);
        if (!$authenticated) {
            return false;
        }
        /** @var RoleData $currData */
        foreach ($this->roleData as $currData) { // Unsetting every other role's session data
            $this->sessionManager->unset($currData->idKey);
        }
        $this->sessionManager->regenerate();
        $this->sessionManager->set($idKey, $id);
        return true;
    }

    /**
     * Logs out all roles and destroys the session.
     */
    public function logout(): void {
        foreach ($this->roleData as $data) {
            $this->sessionManager->unset($data->idKey);
        }
        $this->sessionManager->destroySession();
        $this->sessionManager->start();
    }

    /**
     * Checks if a role is currently authenticated.
     *
     * Validates the session data against the database. Logs out if user
     * no longer exists or is suspended.
     *
     * @param Role $role Role to check.
     * @return bool True if authenticated.
     */
    public function isAuthenticated(Role $role): bool {
        $data = $this->roleData[$role->name] ?? null;
        if ($data === null) return false;
        $id = $this->sessionManager->get($data->idKey);
        if ($id === null) {
            return false;
        }
        if (!$data->service->exists($id)) {
            $this->logout();
            return false;
        }
        if ($role === Role::USER && $data->service->isSuspended($id)) {
            $this->logout();
            return false;
        }
        return true;
    }

    /**
     * Gets the authenticated entity ID for a role.
     *
     * @param Role $role Role to get ID for.
     * @return string|null Entity ID or null if not authenticated.
     */
    public function getId(Role $role): ?string {
        $data = $this->roleData[$role->name] ?? null;
        if ($data === null) return null;
        return $this->sessionManager->get($data->idKey);
    }
}
