<?php
declare(strict_types=1);

namespace Unibostu\Core\Http;

/**
 * Immutable HTTP request wrapper with server data access and middleware attributes.
 */
class Request {
    private string $method;
    private string $uri;
    private array $params;
    private array $body;
    private string $referer;
    private array $attributes = [];
    
    /**
     * Creates a Request from PHP globals.
     *
     * Parses method, URI, query params, and body from $_SERVER, $_GET, $_POST.
     * Body prioritizes JSON input over form data.
     */
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
     * Gets the HTTP method.
     *
     * @return string HTTP method (GET, POST, PUT, DELETE, etc.).
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * Gets the request URI path.
     *
     * @return string URI path without query string.
     */
    public function getUri(): string {
        return $this->uri;
    }
    
    /**
     * Gets a query parameter from the URL.
     *
     * @param string $key Parameter name.
     * @param mixed $default Value if not set.
     * @return mixed Parameter value or default.
     */
    public function get(string $key, $default = null) {
        return $this->params[$key] ?? $default;
    }
    
    /**
     * Gets a value from the request body.
     *
     * @param string $key Body field name.
     * @param mixed $default Default value if not set.
     * @return mixed Body value or default.
     */
    public function post(string $key, $default = null) {
        return $this->body[$key] ?? $default;
    }

    /**
     * Gets the HTTP referer header.
     *
     * @return string Referer URL or empty string if not set.
     */
    public function getReferer(): string {
        return $this->referer;
    }

    /**
     * Gets a middleware-injected attribute.
     *
     * @param RequestAttribute $name Attribute key.
     * @param mixed $default Value if not set.
     * @return mixed Attribute value or default.
     */
    public function getAttribute(RequestAttribute $name, $default = null) {
        return $this->attributes[$name->value] ?? $default;
    }

    /**
     * Returns a new Request with the attribute set.
     *
     * @param RequestAttribute $name Attribute key.
     * @param mixed $value Attribute value.
     * @return self New instance with attribute.
     */
    public function withAttribute(RequestAttribute $name, $value): self {
        $clone = clone $this;
        $clone->attributes[$name->value] = $value;
        return $clone;
    }

    /**
     * Returns a new Request without the specified attribute.
     *
     * @param RequestAttribute $name Attribute key.
     * @return self New instance without attribute.
     */
    public function withoutAttribute(RequestAttribute $name): self {
        $clone = clone $this;
        unset($clone->attributes[$name->value]);
        return $clone;
     }

}
