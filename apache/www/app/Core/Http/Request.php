<?php
declare(strict_types=1);

namespace Unibostu\Core\Http;

/**
 * Class Request
 *
 * A simple HTTP request handler that encapsulates request method, URI, query parameters, and body data.
 */
class Request {
    private string $method;
    private string $uri;
    private array $params;
    private array $body;
    private string $referer;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $parsedUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($parsedUri === false) {
            throw new \RuntimeException("Invalid request URI");
        }
        $this->uri = $parsedUri;
        $this->params = $_GET;
        $input = file_get_contents('php://input');
        $this->body = json_decode($input, true) ?? $_POST;
        $this->referer = $_SERVER['HTTP_REFERER'] ?? '';
    }
    
    /**
     * Get the HTTP method of the request.
     *
     * @return string The HTTP method (e.g., GET, POST).
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * Get the URI of the request.
     *
     * @return string The request URI.
     */ 
    public function getUri(): string {
        return $this->uri;
    }
    
    /**
     * Get a query parameter from the URL.
     *
     * @param string $key The parameter key.
     * @param mixed $default The default value if the parameter is not set.
     * @return mixed The parameter value or default.
     */
    public function get(string $key, $default = null) {
        return $this->params[$key] ?? $default;
    }
    
    /**
     * Get a value from the request body.
     *
     * @param string $key The body key.
     * @param mixed $default The default value if the key is not set.
     * @return mixed The body value or default.
     */
    public function post(string $key, $default = null) {
        return $this->body[$key] ?? $default;
    }

    /**
     * Get the referer URL of the request.
     *
     * @return string The referer URL.
     */
    public function getReferer(): string {
        return $this->referer;
    }
}
