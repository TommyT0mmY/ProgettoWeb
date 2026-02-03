<?php
declare(strict_types=1);

namespace Unibostu\Core\security;

use Unibostu\Core\Http\Request;
use Unibostu\Core\SessionManager;

/**
 * Manages CSRF token generation and validation.
 *
 * Tokens are stored in session and expire after 1 hour. Single-use tokens
 * are invalidated after successful validation.
 */
final class CsrfProtection {
    private const CSRF_TOKEN_LIFETIME = 3600;           // 1 hour
    private const KEY_CSRF_TOKENS = "_cp_csrf_tokens";  // Session key for storing CSRF tokens
    public const KEY_CSRF_KEY = "csrf-key";             // Request parameter name for CSRF token key
    public const KEY_CSRF_TOKEN = "csrf-token";         // Request parameter name for CSRF token value

    // Light probabilistic GC plus hard cap safety
    private const GC_PROBABILITY = 2;    // 2% of calls trigger GC
    private const GC_DIVISOR = 100;
    private const MAX_TOKENS_PER_SESSION = 100; // If above this, force GC

    public function __construct(
        private readonly SessionManager $session
    ) {}

    /**
     * Generates a CSRF token for a given key.
     *
     * @param string $key Unique identifier (e.g., form name).
     * @param bool $multiUse If true, token survives validation.
     * @return string Generated token.
     */
    public function generateToken(string $key, bool $multiUse = false): string {
        $this->maybeGarbageCollect();
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
     * Validates a CSRF token.
     *
     * Single-use tokens are invalidated on successful validation.
     *
     * @param string $key Token identifier.
     * @param string $token Token value to validate.
     * @return bool True if valid.
     */
    public function validateToken(string $key, string $token): bool {
        $this->maybeGarbageCollect();
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
     * Validates CSRF token from request body.
     *
     * Expects 'csrf-key' and 'csrf-token' in the request body.
     *
     * @param Request $request HTTP request.
     * @return bool True if valid.
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

    /**
     * Probabilistic GC: runs with small probability or when too many tokens are stored.
     * Removes expired tokens only, keeping multi-use tokens until they expire.
     */
    private function maybeGarbageCollect(): void {
        $tokens = &self::getTokens();
        $count = count($tokens);

        $shouldRun = (
            $count > self::MAX_TOKENS_PER_SESSION ||
            mt_rand(1, self::GC_DIVISOR) <= self::GC_PROBABILITY
        );

        if (!$shouldRun) {
            return;
        }

        $now = time();
        foreach ($tokens as $k => $data) {
            if ($now > $data['expires']) {
                unset($tokens[$k]);
            }
        }
    }
}
?>
