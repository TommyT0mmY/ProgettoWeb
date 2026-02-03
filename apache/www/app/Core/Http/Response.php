<?php
declare(strict_types=1);

namespace Unibostu\Core\Http;

/**
 * Immutable HTTP response builder.
 */
class Response {
    public function __construct(
        private string $content = '',
        private int $statusCode = 200,
        private array $headers = []
    ) {}

    /**
     * @return self New instance.
     */
    public static function create(): self {
        return new self();
    }

    /**
     * @param string $content Response body.
     * @return self New instance with content.
     */
    public function withContent(string $content): self {
        $clone = clone $this;
        $clone->content = $content;
        return $clone;
    }

    /**
     * @param int $statusCode HTTP status code.
     * @return self New instance with status.
     */
    public function withStatusCode(int $statusCode): self {
        $clone = clone $this;
        $clone->statusCode = $statusCode;
        return $clone;
    }

    /**
     * @param string $name Header name.
     * @param string $value Header value.
     * @return self New instance with header.
     */
    public function withHeader(string $name, string $value): self {
        $clone = clone $this;
        $clone->headers[$name] = $value;
        return $clone;
    }

    /**
     * Appends to existing header if present, otherwise sets it.
     *
     * @param string $name Header name.
     * @param string $value Header value to append.
     * @return self New instance with header.
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
     * @param string $name Header name.
     * @return self New instance without header.
     */
    public function withoutHeader(string $name): self {
        $clone = clone $this;
        unset($clone->headers[$name]);
        return $clone;
    }

    /**
     * Sets JSON content with Content-Type header.
     *
     * @param array $data Data to JSON encode.
     * @param int|null $status Optional status code override, this is a shorthand for withStatusCode().
     * @return self New instance with JSON content.
     */
    public function json(array $data, ?int $status = null): self {
        return $this
            ->withStatusCode($status ?? $this->statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->withContent(json_encode($data));
    }

    /**
     * Creates a redirect response.
     *
     * @param string $url Redirect URL.
     * @param int $status HTTP status code (default 302).
     * @return self New instance configured for redirect.
     */
    public function redirect(string $url, int $status = 302): self {
        return $this
            ->withStatusCode($status)
            ->withHeader('Location', $url);
    }
 
    /**
     * Sends the response to the client.
     */
    public function send(): void {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->content;
    }
}
