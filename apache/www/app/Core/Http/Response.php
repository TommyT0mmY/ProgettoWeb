<?php
declare(strict_types=1);

namespace Unibostu\Core\Http;

/**
 * Class Response
 *
 * A simple HTTP response handler that encapsulates response content, status code, and headers.
 */
class Response {
    public function __construct(
        private string $content = '',
        private int $statusCode = 200,
        private array $headers = []
    ) {}

    public static function create(): self {
        return new self();
    }

    /**
     * Set the response content immutably.
     *
     * @param string $content The content to set.
     * @return self A new Response instance with the updated content.
     */
    public function withContent(string $content): self {
        $clone = clone $this;
        $clone->content = $content;
        return $clone;
    }

    /**
     * Set the HTTP status code immutably.
     *
     * @param int $statusCode The status code to set.
     * @return self A new Response instance with the updated status code.
     */
    public function withStatusCode(int $statusCode): self {
        $clone = clone $this;
        $clone->statusCode = $statusCode;
        return $clone;
    }

    /**
     * Set a header immutably.
     *
     * @param string $name The header name.
     * @param string $value The header value.
     * @return self A new Response instance with the updated header.
     */
    public function withHeader(string $name, string $value): self {
        $clone = clone $this;
        $clone->headers[$name] = $value;
        return $clone;
    }

    /**
     * Add a header immutably (appends to existing header if present).
     *
     * @param string $name The header name.
     * @param string $value The header value to add.
     * @return self A new Response instance with the added header.
     */
    public function withAddedHeader(string $name, string $value): self {
        $clone = clone $this;
        if (isset($clone->headers[$name])) {
            $clone->headers[$name] .= ', ' . $value;
        } else {
            $clone->headers[$name] = $value;
        }
        return $clone;
    }

    /**
     * Remove a header immutably.
     *
     * @param string $name The header name to remove.
     * @return self A new Response instance without the specified header.
     */
    public function withoutHeader(string $name): self {
        $clone = clone $this;
        unset($clone->headers[$name]);
        return $clone;
    }

    /**
     * Set JSON content with appropriate headers.
     *
     * @param array $data The data to encode as JSON.
     * @param int $status The HTTP status code.
     * @return self A new Response instance with JSON content and headers.
     */
    public function json(array $data, int $status = 200): self {
        return $this
            ->withStatusCode($status)
            ->withHeader('Content-Type', 'application/json')
            ->withContent(json_encode($data));
    }

    /**
     * Set a redirect response.
     *
     * @param string $url The URL to redirect to.
     * @param int $status The HTTP status code for the redirect.
     * @return self A new Response instance configured for redirection.
     */
    public function redirect(string $url, int $status = 302): self {
        return $this
            ->withStatusCode($status)
            ->withHeader('Location', $url);
    }
 
    /**
     * Send the response to the client.
     */
    public function send(): void {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->content;
    }
}
