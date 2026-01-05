<?php
namespace Unibostu\Core\router;

use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Controller\BaseController;
use Unibostu\Core\Container;
use Unibostu\Core\LogHelper;

/**
 * Prefix used to identify variable route segments (e.g. ":id").
 */
const VARIABLE_PREFIX = ':';

/** Callback information for a registered route.
 *
 * Contains:
 * - {@see $controllerClassname}: Fully qualified class name of the controller.
 * - {@see $action}: Method name to be invoked on the controller.
 */
readonly class Callback {
    public function __construct(
        /** @var class-string<\Unibostu\Controller\BaseController> */
        public string $controllerClassname,
        /** @var string */
        public string $action
    ) {}
}

/**
 * Node of the prefix tree (trie) used to store routes.
 *
 * Each node may contain:
 * - concrete children in {@see $children}, indexed by literal segment names;
 * - variable children in {@see $variables}, indexed by variable names (without prefix);
 * - the {@see $isEnding} flag indicating whether the node represents the end of a valid route;
 * - the {@see $callback} associated with the route ending at this node.
 *
 * For debugging purposes, the class implements {@see JsonSerializable}
 */
class Node implements \JsonSerializable {
    public array $children = [];
    public array $variables = [];
    public bool $isEnding = false;
    public ?Callback $callback = null;

    public function jsonSerialize():mixed {
        return get_object_vars($this);
    }
}

/**
 * Result of the backtracking search during route dispatching.
 *
 * Contains:
 * - {@see $found}: indicates whether a matching route was found.
 * - {@see $callback}: the callback associated with the matched route.
 * - {@see $params}: associative array of variable names and their corresponding values.
 */
class BacktrackingResult {
    public bool $found = false;
    public ?Callback $callback = null;
    public array $params = [];
}

/**
 * HTTP Router for registering routes and dispatching requests.
 *
 * Register routes using HTTP method methods (get, post, etc.) and provide
 * a callback function. When a route matches an incoming request, the router
 * invokes the callback with route variables as an associative array and the
 * request object.
 *
 * Variable routes are defined with a colon prefix (e.g., ":id").
 *
 * Example: For route "/users/:id", matching "/users/123" invokes
 * the callback with $params = ['id' => '123'] and the request object.
 *
 */
class Router {
    /**
     * Root of the trie containing all registered routes.
     */
    private Node $routes; 
    
    public function __construct() {
        $this->routes = new Node();
    }

    /**
     * Adds a route to the router.
     *
     * Rules:
     * - {@code $method} and {@code $path} must not be empty; otherwise an exception
     *  is thrown.
     * - Empty segments are not allowed and will throw a RuntimeException.
     * - Segments starting with {@see VARIABLE_PREFIX} are treated as variables.
     * - Duplicate variable names in the same route are not allowed; attempting to do so
     * will throw a RuntimeException.
     * - Already defined routes cannot be redefined; attempting to do so will throw
     *  a RuntimeException.
     *
     * @param string  $method   HTTP method (e.g. "GET", "POST").
     * @param string  $path     Route path (e.g. "/users/:id").
     * @param class-string<BaseController> $controllerClassname    Fully qualified class name of the controller.
     * @param string  $action   Method name to be invoked on the controller.
     */
    public function add(string $method, string $path, string $controllerClassname, string $action): void {
        if (empty($method) || empty($path)) {
            LogHelper::logError("Empty request method or path");
            throw new \RuntimeException("Empty request method or path");
        }
        $seenVariables = []; // Tracking seen variables to prevent duplicates
        $node = &$this->routes;
        $route = Router::getRoute($method, $path);
        foreach(explode('/', $route) as $routeSegment) {
            if (empty($routeSegment)) {
                LogHelper::logError("Empty route segment");
                throw new \RuntimeException("Empty route segment");
            }
            $isVariable = $routeSegment[0] === VARIABLE_PREFIX;
            $key = $isVariable ? substr($routeSegment, 1) : $routeSegment;
            $bucket = $isVariable ? "variables" : "children";
            if (in_array($key, $seenVariables, true)) {
                LogHelper::logError("Duplicate variable name in route: '$key'");
                throw new \RuntimeException("Duplicate variable name in route: '$key'");
            }
            if (!isset($node->{$bucket}[$key])) {
                $node->{$bucket}[$key] = new Node();
            }
            $node = &$node->{$bucket}[$key];
            $seenVariables[] = $key;
        }
        if ($node->isEnding) {
            LogHelper::logError("Duplicate routes");
            throw new \RuntimeException("Duplicate routes");
        }
        $node->isEnding = true;
        $node->callback = new Callback($controllerClassname, $action);
    }

    /**
     * Dispatches a request to the matching route.
     * Variables defined in the route (prefixed with {@see VARIABLE_PREFIX}) are
     * passed to the controller action as an associative array {@code $params}.
     *
     * Example:
     * - Registered route: "/users/:id"
     * - Incoming request path: "/users/123"
     * - The controller action is invoked with $params = ['id' => '123'] and the
     * {@code $request} object.
     *
     * @param Request   $request   Incoming HTTP request.
     * @param Container $container Dependency injection container.
     *
     * @return Response The HTTP response generated by the controller.
     *
     * @throws \RuntimeException On empty parameters or if no matching route is found.
     */
    public function dispatch(Request $request, Container $container): Response {
        $method = $request->getMethod();
        $path = $request->getUri();
        if (empty($method) || empty($path)) {
            throw new \RuntimeException("Empty request method or URI");
        }
        $route = Router::getRoute($method, $path);
        $segments = explode('/', $route);
        if (empty($segments)) {
            throw new \RuntimeException("Empty route segments");
        }
        $result = $this->dispatchDFS($this->routes, $segments, 0);
        if (!$result->found || !isset($result->callback)) {
            throw new \RuntimeException("404 Not Found\n");
        }
        $controllerName = $result->callback->controllerClassname;
        $action = $result->callback->action;
        $controller = $this->istantiateController($controllerName, $container);
        return $controller->$action($result->params, $request);
    }

    private function dispatchDFS(Node $node, array $segments, int $segmentIndex): BacktrackingResult {
        if ($segmentIndex >= count($segments)) { // If at last segment, check if this is an ending node
            $result = new BacktrackingResult();
            if ($node->isEnding) {
                $result->found = true;
                $result->callback = $node->callback;
            }
            return $result;
        }
        $currentSegment = $segments[$segmentIndex]; // Segment to match
        $childNode = $node->children[$currentSegment] ?? null; // Trying concrete child first
        if (isset($childNode)) {
            $result = $this->dispatchDFS($childNode, $segments, $segmentIndex + 1);
            if ($result->found) {
                return $result;
            }
        }
        foreach ($node->variables as $varName => $varNode) { // Trying variable children
            $result = $this->dispatchDFS($varNode, $segments, $segmentIndex + 1);
            if ($result->found) {
                $result->params[$varName] = $currentSegment; // Store this variable value
                return $result;
            }
        }
        return new BacktrackingResult(); // No match
    }
    
    /**
     * @return string Normalized route string.
     */
    private static function getRoute(string $method, string $path): string {
        $route = $method . $path;
        $route = trim($route); // Whitespaces
        $route = rtrim($route, '/'); // Trailing slashes
        return $route;
    }

    /**
     * Creates a controller instance using the provided container.
     *
     * @param class-string<BaseController> $controllerClassname    Fully qualified class name of the controller.
     * @param Container $container           Dependency injection container.
     *
     * @return BaseController Instance of the requested controller.
     *
     * @throws \RuntimeException If the controller class does not exist or cannot be instantiated.
     */
    private function istantiateController(string $className, Container $container): BaseController {
        if (!class_exists($className)) {
            throw new \RuntimeException("Controller class '$className' does not exist");
        }
        return new $className($container);
    }
}

