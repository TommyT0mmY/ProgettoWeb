<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

/**
 * Interface RoleService
 *
 * Defines methods for role-based services such as user and admin services.
 */
interface RoleService {
    /**
     * Checks if the provided credentials are valid.
     *
     * @param string $id The account ID.
     * @param string $password The password.
     *
     * @return bool True if credentials are valid, false otherwise.
     */
    public function checkCredentials(string $id, string $password): bool;

    /**
     * Checks if an account with the given ID exists.
     *
     * @param string $id The account ID.
     *
     * @return bool True if the account exists, false otherwise.
     */
    public function exists(string $id): bool;

    /**
     * Checks if the account with the given ID is suspended.
     *
     * @param string $id The account ID.
     *
     * @return bool True if the account is suspended, false otherwise.
     */
    public function isSuspended(string $id): bool;
}
