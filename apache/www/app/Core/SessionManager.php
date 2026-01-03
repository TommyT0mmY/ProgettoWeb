<?php
declare(strict_types=1);

namespace Unibostu\Core;

final class SessionManager {
    private static ?self $instance = null;
    /** Interval for periodic session ID regeneration (30 mins). */
    private const REGENERATION_INTERVAL = 1800;
    /** Tolerance window (5 mins) for late requests after an ID change. */
    private const EXPIRATION_TOLERANCE = 300;
    // Session metadata keys
    private const KEY_CREATED = "_sm_created";
    private const KEY_AGENT = "_sm_agent";
    private const KEY_DESTROYED = "_sm_destroyed";
    private const KEY_NEW_SESSID = "_sm_new_sessid";
    /** @var bool Internal flag to track session_start() status. */
    private bool $started = false;

    private function __construct() {
        $this->start();
    }

    private function __clone() {
    }

    /**
     * Get the singleton instance.
     *
     * @return self The instance.
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function start(): void {
        $this->assertHeadersNotSent();
        if ($this->started) {
            return;
        }
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new \RuntimeException("Sessions are disabled on this server.");
        }
        if (session_status() === PHP_SESSION_ACTIVE && !isset($_SESSION[self::KEY_CREATED])) {
            throw new \RuntimeException("A session is already active without proper initialization.");
        }
        session_start();
        $this->started = true;
        // Is this a new session 
        if (!isset($_SESSION[self::KEY_CREATED])) {
            $_SESSION[self::KEY_CREATED] = time();
            $_SESSION[self::KEY_AGENT] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }
        $destroyedTime = $_SESSION[self::KEY_DESTROYED] ?? time();
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $sessionUserAgent = $_SESSION[self::KEY_AGENT] ?? '';
        // Checking if the session is expired (destroyed long ago)
        if ($destroyedTime + self::EXPIRATION_TOLERANCE  < time()) {
            $this->destroySession();
            throw new \RuntimeException('Session expired or invalid');
        }
        // Check User-Agent consistency
        if ($sessionUserAgent !== $currentUserAgent) {
            $this->destroySession();
            throw new \RuntimeException('User-Agent mismatch detected');
        }
        // Check if a regeneration is already in progress
        if ($this->isSessionRegenerating()) {
            $this->completeSessionRegeneration();
            return;
        }
        // Periodic regeneration
        $creationTime = $_SESSION[self::KEY_CREATED];
        if ($creationTime + self::REGENERATION_INTERVAL < time()) {
            $this->regenerate();
            return;
        }
    }

    public function regenerate(): void {
        $this->assertCanUseSession();
        if ($this->isSessionRegenerating()) {
            $this->completeSessionRegeneration();
            return;
        }
        $newSessionId = session_create_id();
        $data = $_SESSION;
        unset($data[self::KEY_CREATED], $data[self::KEY_NEW_SESSID], $data[self::KEY_DESTROYED]); // Cleaning data for the new session preventing bugs
        $_SESSION[self::KEY_NEW_SESSID] = $newSessionId;
        $_SESSION[self::KEY_DESTROYED] = time();
        $this->setNewSessionId($newSessionId);
        $_SESSION = array_merge($_SESSION, $data);
        $_SESSION[self::KEY_CREATED] = time();
    }

    private function completeSessionRegeneration(): void {
        if (!$this->isSessionRegenerating()) {
            return;
        }
        $newSessionId = $_SESSION[self::KEY_NEW_SESSID];
        $this->setNewSessionId($newSessionId);
    }

    private function setNewSessionId(string $newSessionId): void {
        session_write_close();
        session_id($newSessionId);
        session_start();
        unset($_SESSION[self::KEY_DESTROYED], $_SESSION[self::KEY_NEW_SESSID]);
    }

    private function isSessionRegenerating(): bool {
        return isset($_SESSION[self::KEY_DESTROYED]) && isset($_SESSION[self::KEY_NEW_SESSID]);
    }

    public function destroySession(): void {
        $this->assertCanUseSession();
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_unset();
        session_destroy();
        $this->started = false;
    }

    /**
     * Set a session value.
     * 
     * @param string $key The session key
     * @param mixed $value The value to set
     */
    public function set(string $key, $value): void {
        $this->assertCanUseSession();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     * 
     * @param string $key The session key
     * @return mixed|null Returns the value or null if not exists
     */
    public function get(string $key) {
        $this->assertCanUseSession();
        return $_SESSION[$key] ?? null;
    }

    /**
     * Get a reference to a session value.
     *
     * Allows direct modification: $sessionManager->get("metadata")["id"] = 123;
     * 
     * @param string $key The session key
     * @return mixed|null Returns a reference to the variable 
     */
    public function &getRef(string $key) {
        $this->assertCanUseSession();
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = null;
        }
        return $_SESSION[$key];
    }

    /**
     * Check if a session key is set.
     * 
     * @param string $key The session key
     * @return bool True if set, false otherwise
     */
    public function isset(string $key): bool {
        $this->assertCanUseSession();
        return isset($_SESSION[$key]);
    }

    /**
     * Unset a session key.
     * 
     * @param string $key The session key
     */
    public function unset(string $key): void {
        $this->assertCanUseSession();
        unset($_SESSION[$key]);
    }

    /**
     * Ensure headers have not been sent yet.
     *
     * @throws \RuntimeException if headers already sent.
     */
    private function assertHeadersNotSent() {
        if (headers_sent($file, $line)) {
            LogHelper::logError("Headers already sent in $file on line $line");
            throw new \RuntimeException("Headers already sent in $file on line $line");
        }
    }

    /**
     * Call to ensure that the session is ready for use. 
     * An exception is thrown if the session is not started or headers already sent.
     *
     * @throws \RuntimeException if session not started or headers already sent.
     */
    private function assertCanUseSession() {
        $this->assertHeadersNotSent();
        if (!$this->started) {
            LogHelper::logError("Session has not been started.");
            throw new \RuntimeException("Session has not been started.");
        }
    }
}

