<?php
declare(strict_types=1);

namespace Unibostu\Core\security;
use Unibostu\Core\SessionManager;
use Unibostu\Model\Service\AdminService;
use Unibostu\Model\Service\UserService;

class Auth {
    private const KEY_USERID = 'user_id';
    private const KEY_ADMINID = 'admin_id';

    private UserService $userService;
    private AdminService $adminService;
    
    public function __construct(
        private SessionManager $sessionManager
    ) {
        $this->userService = new UserService();
        $this->adminService = new AdminService();
    }

    public function loginAsUser(string $username, string $password): bool {
        if ($this->isAuthenticatedAsUser()) {
            $this->logout();
        }
        try {
            $authenticated = $this->userService->authenticate($username, $password);
            if ($authenticated) {
                $this->sessionManager->regenerate();
                $this->sessionManager->set(self::KEY_USERID, $username);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function loginAsAdmin(string $adminId, string $password): bool {
        if ($this->isAuthenticatedAsAdmin()) {
            $this->logout();
        }
        try {
            $authenticated = $this->adminService->authenticate($adminId, $password);
            if ($authenticated) {
                $this->sessionManager->regenerate();
                $this->sessionManager->set(self::KEY_ADMINID, $adminId);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function logout(): void {
        $this->sessionManager->unset(self::KEY_USERID);
        $this->sessionManager->unset(self::KEY_ADMINID);
        $this->sessionManager->destroySession();
        $this->sessionManager->start();
    }

    public function isAuthenticatedAsUser(): bool {
        $userId = $this->sessionManager->get(self::KEY_USERID);
        if ($userId === null) {
            return false;
        }
        if (!$this->userService->userExists($userId)) {
            $this->logout();
            return false;
        }
        if ($this->userService->isUserSuspended($userId)) {
            $this->logout();
            return false;
        }
        return true;
    }

    public function isAuthenticatedAsAdmin(): bool {
        $adminId = $this->sessionManager->get(self::KEY_ADMINID);
        if ($adminId === null) {
            return false;
        }
        if (!$this->adminService->adminExists($adminId)) {
            $this->logout();
            return false;
        }
        return true;
    }

    public function getUserId(): string {
        return $this->sessionManager->get(self::KEY_USERID);
    }

    public function getAdminId(): string {
        return $this->sessionManager->get(self::KEY_ADMINID);
    }
}
