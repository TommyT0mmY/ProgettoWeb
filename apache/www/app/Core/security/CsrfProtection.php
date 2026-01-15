<?php
declare(strict_types=1);

namespace Unibostu\Core\security;

use Unibostu\Core\Http\Request;
use Unibostu\Core\SessionManager;

/**
 * Class CsrfProtection
 *
 * Manages CSRF token generation and validation to protect against 
 * Cross-Site Request Forgery attacks.
 */
final class CsrfProtection {
    private const CSRF_TOKEN_LIFETIME = 3600;           // 1 hour
    private const KEY_CSRF_TOKENS = "_cp_csrf_tokens";
    public const KEY_CSRF_KEY = "csrf-key";
    public const KEY_CSRF_TOKEN = "csrf-token";

    public function __construct(
        private readonly SessionManager $session
    ) {}

    /**
     * Generates a CSRF token for a given key.
     *
     * @param string $key Identifier for the token (e.g., form name).
     * @param bool $multiUse Whether the token can be used multiple times.
     * @return string The generated CSRF token.
     */
    public function generateToken(string $key, bool $multiUse = false): string {
        $token = bin2hex(random_bytes(32));
        self::getTokens()[$key] = [
            'token' => $token,
            'created' => time(),
            'expires' => time() + self::CSRF_TOKEN_LIFETIME,
            'multiUse' => $multiUse
        ];
        return $token;
    }

    /**
     * Validates a CSRF token for a given key.
     *
     * @param string $key Identifier for the token (e.g., form name).
     * @param string $token The token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public function validateToken(string $key, string $token): bool {
        if (!isset(self::getTokens()[$key])) {
            return false;
        }
        $stored = self::getTokens()[$key];
        if (time() > $stored['expires']) {
            unset(self::getTokens()[$key]);
            return false;
        }
        $isValid = hash_equals($stored['token'], $token);
        if ($isValid && !$stored['multiUse']) {
            unset(self::getTokens()[$key]);
        }
        return $isValid;
    }

    /**
     * Validates CSRF token from an HTTP request.
     *
     * Expects the request to contain 'csrf-key' and 'csrf-token' parameters in the request body.
     *
     * @param Request $request The HTTP request to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public function validateRequest(Request $request): bool {
        $key = $request->post(self::KEY_CSRF_KEY);
        $token = $request->post(self::KEY_CSRF_TOKEN);
        if ($key === null || $token === null) {
            return false;
        }
        return $this->validateToken($key, $token);
    }

    /**
     * Invalidates a CSRF token for a given key.
     *
     * @param string $key Identifier for the token to invalidate.
     */
    public function invalidateToken(string $key): void {
        unset(self::getTokens()[$key]);
    }

    /**
     * Invalidates CSRF token from an HTTP request.
     *
     * Expects the request to contain a 'csrf-key' parameter in the request body.
     *
     * @param Request $request The HTTP request to process.
     */
    public function invalidateRequestToken(Request $request): void {
        $key = $request->post(self::KEY_CSRF_KEY);
        if ($key !== null) {
            $this->invalidateToken($key);
        }
    }

    /**
     * Internal helper to access session storage.
     *
     * Ensures the session is started and initializes the token registry 
     * if it does not already exist. 
     *
     * @return array<string, array> Reference to the tokens array in session.
     */
    private function &getTokens(): array {
        if (!$this->session->isset(self::KEY_CSRF_TOKENS)) {
            $this->session->set(self::KEY_CSRF_TOKENS, []);
        }
        return $this->session->getRef(self::KEY_CSRF_TOKENS);
    }
}
?>
