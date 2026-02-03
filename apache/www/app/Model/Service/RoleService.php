<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

/**
 * Interface for role-based authentication services.
 * 
 * @see UserService
 * @see AdminService
 */
interface RoleService {
    /**
     * Validates credentials for an account.
     *
     * @param string $id Account identifier.
     * @param string $password Plain text password.
     * @return bool True if valid.
     */
    public function checkCredentials(string $id, string $password): bool;

    /**
     * Checks if an account exists.
     *
     * @param string $id Account identifier.
     * @return bool True if exists.
     */
    public function exists(string $id): bool;

    /**
     * Checks if the account with the given ID is suspended.
     *
     * @param string $id The account ID.
     * @return bool True if the account is suspended, false otherwise.
     */
    public function isSuspended(string $id): bool;
}
