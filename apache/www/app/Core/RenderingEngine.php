<?php
declare(strict_types=1);

namespace Unibostu\Core;

use Unibostu\Core\security\CsrfProtection;

/**
 * Template rendering engine for the view layer.
 *
 * Views can extend Layouts via `$this->extend()` and include Components
 * via `$this->component()`. The rendered Layout receives the View content
 * in the `$content` variable.
 */
class RenderingEngine {
    private const basePath = __DIR__ . '/../View/';
    private const layoutsPath = self::basePath . 'layouts/';
    private const viewsPath = self::basePath . 'views/';
    private const componentsPath = self::basePath . 'components/';

    private CsrfProtection $csrfProtection;
    private ?string $layout = null;
    private array $layoutData = [];
    private bool $isRendering = false;

    public function __construct(
        private Container $container
    ) {
        $this->csrfProtection = $this->container->get(CsrfProtection::class); 
    }

    /**
     * Renders a view with optional data.
     *
     * If the view calls extend(), the output is wrapped in the specified layout.
     *
     * @param string $viewName View filename without extension, relative to views/ directory.
     * @param array $data Variables to extract into view scope.
     * @return string Rendered HTML.
     */
    public function render(string $viewName, array $data = []): string {
        extract($data);
        ob_start();
        $this->isRendering = true;
        include self::viewsPath . $viewName . ".php";
        $this->isRendering = false;
        $content = ob_get_clean();
        // If a layout is set, render it with the content inside
        if ($this->layout) {
            return $this->renderLayout($this->layout, array_merge($this->layoutData, ['content' => $content]));
        }
        return $content;
    }

    private function renderLayout(string $layoutName, array $data = []): string {
        extract($data);
        ob_start();
        $this->isRendering = true;
        include self::layoutsPath . $layoutName . ".php";
        $this->isRendering = false;
        return ob_get_clean();
    }

    /**
     * Includes a component within a view.
     *
     * @param string $name Component filename without extension, relative to components/ directory.
     * @param array $data Variables to extract into component scope.
     * @throws \RuntimeException If called outside of render().
     */
    public function component(string $name, array $data = []): void {
        if (!$this->isRendering) {
            throw new \RuntimeException("Components can only be rendered within a view.");
        }
        extract($data);
        include self::componentsPath . $name . ".php";
    }
    
    /**
     * Sets the layout to wrap the view. Called within a view file.
     *
     * @param string $layout Layout filename without extension, relative to layouts/ directory.
     * @param array $data Variables to extract into layout scope.
     */
    public function extend(string $layout, array $data = []): void {
        $this->layout = $layout;
        $this->layoutData = $data;
    }

    /**
     * Generates a CSRF key/token pair for forms.
     *
     * @param bool $multiUse If true, token can be reused.
     * @return array{csrfKey: string, csrfToken: string}
     */
    public function generateCsrfPair(bool $multiUse = false): array {
        $key = bin2hex(random_bytes(16));
        return [
            'csrfKey' => $key,
            'csrfToken' => $this->csrfProtection->generateToken($key, $multiUse)
        ];
    }
}
