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
    private array $attributes = [];
    
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
     * Get a custom attribute.
     *
     * @param string $name The attribute name.
     * @param mixed $default The default value if the attribute is not set.
     * @return mixed The attribute value or default.
     */
    public function getAttribute(string $name, $default = null) {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Set a custom attribute.
     *
     * @param string $name The attribute name.
     * @param mixed $value The attribute value.
     * @return self A new instance with the specified attribute set.
     */
    public function withAttribute(string $name, $value): self {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
     * Remove a custom attribute.
     *
     * @param string $name The attribute name to remove.
     * @return self A new instance without the specified attribute.
     */
    public function withoutAttribute(string $name): self {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        return $clone;
     }

}
