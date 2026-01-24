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

class Auth {
    private array $roleData = [];

    public function __construct(
        private SessionManager $sessionManager
    ) {
        $roleData = [
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

    public function logout(): void {
        foreach ($this->roleData as $data) {
            $this->sessionManager->unset($data->idKey);
        }
        $this->sessionManager->destroySession();
        $this->sessionManager->start();
    }

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

    public function getId(Role $role): ?string {
        $data = $this->roleData[$role->name] ?? null;
        if ($data === null) return null;
        return $this->sessionManager->get($data->idKey);
    }
}
