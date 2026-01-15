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

    /**
     * Performs user login.
     *
     * @param string $userId The userId.
     * @param string $password The user password.
     *
     * @return bool True if login is successful, false otherwise.
     */
    public function loginAsUser(string $userId, string $password): bool {
        $authenticated = $this->userService->checkCredentials($userId, $password);
        if (!$authenticated) {
            return false;
        }
        $this->sessionManager->unset(self::KEY_USERID);
        $this->sessionManager->unset(self::KEY_ADMINID);
        $this->sessionManager->regenerate();
        $this->sessionManager->set(self::KEY_USERID, $userId);
        return true;
    }

    /**
     * Performs admin login.
     *
     * @param string $adminId The admin ID.
     * @param string $password The admin password.
     *
     * @return bool True if login is successful, false otherwise.
     */
    public function loginAsAdmin(string $adminId, string $password): bool {
        $authenticated = $this->adminService->checkCredentials($adminId, $password);
        if (!$authenticated) {
            return false;
        }
        $this->sessionManager->unset(self::KEY_USERID);
        $this->sessionManager->unset(self::KEY_ADMINID);
        $this->sessionManager->regenerate();
        $this->sessionManager->set(self::KEY_ADMINID, $adminId);
        return true;
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
