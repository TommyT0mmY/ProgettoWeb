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
     *
     * Set the response content.
     *
     * @param string $content The content to set.
     * @return self
     */
    public function setContent(string $content): self {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the HTTP status code.
     *
     * @param int $statusCode The status code to set.
     * @return self
     */
    public function setStatusCode(int $statusCode): self {
        $this->statusCode = $statusCode;
        return $this;
    }
 
    /**
     * Set a response header.
     *
     * @param string $name  The name of the header.
     * @param string $value The value of the header.
     * @return self
     */
    public function setHeader(string $name, string $value): self {
        $this->headers[$name] = $value;
        return $this;
    }
 
    /**
     * Set JSON content and appropriate header.
     *
     * @param array $data The data to encode as JSON.
     * @return self
     */
    public function json(array $data): self {
        $this->content = json_encode($data);
        $this->headers['Content-Type'] = 'application/json';
        return $this;
    }
 
    /**
     * Redirect to a different URL.
     *
     * @param string $url The URL to redirect to.
     * @return self
     */
    public function redirect(string $url): self {
        $this->statusCode = 302; // Temporary Redirect
        $this->headers['Location'] = $url;
        return $this;
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
