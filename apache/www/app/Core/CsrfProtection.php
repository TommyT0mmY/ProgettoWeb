<?php
declare(strict_types=1);

namespace Unibostu\Core;

/**
 * Utility class for Cross-Site Request Forgery (CSRF) protection.
 *
 * This class manages the generation and validation of security tokens
 * stored in the user's session. It prevents unauthorized commands from 
 * being transmitted from a user that the web application trusts.
 *
 * Each token is tied to a specific form ID and has a limited lifetime.
 * For security, tokens are "one-time use" and are destroyed upon 
 * successful validation.
 */
final class CsrfProtection {
    private static SessionManager $session = SessionManager::getInstance();

    private const CSRF_TOKEN_LIFETIME = 3600;           // 1 hour
    private const KEY_CSRF_TOKENS = "_cp_csrf_tokens";

    private function __construct() {
        throw new \Exception('Not implemented');
    }

    /**
     * Generates a unique CSRF token for a specific form.
     *
     * The generated token is stored in the session along with its 
     * creation and expiration timestamps.
     *
     * @param string $formId Unique identifier for the form (e.g., "login_form").
     * @return string The generated 64-character hexadecimal token.
     */
    public static function generateToken(string $formId): string {
        $token = bin2hex(random_bytes(32));
        self::getTokens()[$formId] = [
            'token' => $token,
            'created' => time(),
            'expires' => time() + self::CSRF_TOKEN_LIFETIME
        ];
        return $token;
    } 

    /**
     * Validates a CSRF token against session records.
     *
     * Rules:
     * - Returns {@code false} if no token exists for the given {@code $formId};
     * - Returns {@code false} and clears the record if the token has expired;
     * - Uses {@see hash_equals()} for timing-safe comparison to prevent side-channel attacks;
     * - If validation succeeds, the token is destroyed (One-Time Token logic).
     *
     * @param string $token  The token string to verify.
     * @param string $formId The identifier of the form that issued the token.
     * @return bool True if the token is valid and not expired.
     */
    public static function validateToken(string $token, string $formId): bool {
        if (!isset(self::getTokens()[$formId])) {
            return false;
        }
        $stored = self::getTokens()[$formId];
        if (time() > $stored['expires']) {
            unset(self::getTokens()[$formId]);
            return false;
        }
        $isValid = hash_equals($stored['token'], $token);
        if ($isValid) {
            unset(self::getTokens()[$formId]);
        }
        return $isValid;
    }

    /**
     * Internal helper to access session storage.
     *
     * Ensures the session is started and initializes the token registry 
     * if it does not already exist. 
     *
     * @return array<string, array> Reference to the tokens array in session.
     */
    private static function &getTokens(): array {
        if (!self::$session->isset(self::KEY_CSRF_TOKENS)) {
            self::$session->set(self::KEY_CSRF_TOKENS, []);
        }
        return self::$session->getRef(self::KEY_CSRF_TOKENS);
    }
}
?>
