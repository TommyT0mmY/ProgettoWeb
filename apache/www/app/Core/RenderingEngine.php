<?php
declare(strict_types=1);

namespace Unibostu\Core;

use Unibostu\Core\security\CsrfProtection;

/**
 * Class RenderingEngine
 *
 * A simple rendering engine for the view layer. 
 *
 * Layouts are the outermost structure, they indirectly include Views
 * through the `$content` variable.
 * Views are the main content and can include Components, they can also
 * specify a Layout to wrap themselves in with `$this->extend()`.
 * Components are reusable pieces of UI that can be included in Views.
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
     * Render a View with optional data.
     *
     * @param string $viewName The name of the View to render.
     * @param array $data Associative array of data to be extracted into the View.
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
     * Include a component within a View.
     *
     * @param string $name The name of the component to include.
     * @param array $data Associative array of data to be extracted into the component.
     */
    public function component(string $name, array $data = []): void {
        if (!$this->isRendering) {
            throw new \RuntimeException("Components can only be rendered within a view.");
        }
        extract($data);
        include self::componentsPath . $name . ".php";
    }
    
    /**
     * Define which layout to use for rendering. (Called within a view)
     *
     * @param string $layout The name of the layout to use.
     * @param array $data Associative array of data to be extracted into the layout.
     */
    public function extend(string $layout, array $data = []): void {
        $this->layout = $layout;
        $this->layoutData = $data;
    }

    public function generateCsrfPair(bool $multiUse = false): array {
        $key = bin2hex(random_bytes(16));
        return [
            'csrfKey' => $key,
            'csrfToken' => $this->csrfProtection->generateToken($key, $multiUse)
        ];
    }
}
