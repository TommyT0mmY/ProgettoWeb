<?php
namespace Unibostu\Core;
use Closure;

/**
 * Prefix used to identify variable route segments (e.g. ":id").
 *
 * When a route segment starts with this prefix, the router stores it
 * inside {@see Node::$variables} instead of {@see Node::$children}.
 */
const VARIABLE_PREFIX = ':';

/**
 * Node of the prefix tree (trie) used to store routes.
 *
 * Each node may contain:
 * - concrete children in {@see $children}, indexed by literal segment names;
 * - variable children in {@see $variables}, indexed by variable names (without prefix);
 * - the {@see $isEnding} flag indicating whether the node represents the end of a valid route;
 * - a {@see $callback} closure associated with the terminating route.
 *
 * For debugging purposes, the class implements {@see JsonSerializable}
 */
class Node implements \JsonSerializable {
    public array $children = [];
    public array $variables = [];
    public bool $isEnding = false;
    public ?Closure $callback = null;

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
    public ?Closure $callback = null;
    public array $params = [];
}

/**
 * HTTP Router for registering routes and dispatching requests.
 *
 * Register routes using HTTP method methods (get, post, etc.) and provide
 * a callback function. When a route matches an incoming request, the router
 * invokes the callback with route variables as an associative array.
 *
 * Variable routes are defined with a colon prefix (e.g., ":id").
 * Example: For route "/users/:id", matching "/users/123" invokes
 * $callback(['id' => '123']).
 *
 * Usage:
 * $router = new Router();
 * $router->get('/users/:id', function($params) {
 *     // $params is ['id' => '123'] for "/users/123"
 * });
 * $router->dispatch('GET', '/users/123');
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
     * Registers a GET route.
     *
     * @param string  $path     Route path (e.g. "/users/:id").
     * @param Closure $callback Callback that receives route variables as an associative array.
     *                          Example: For route "/users/:id", matching "/users/123" invokes
     *                          $callback(['id' => '123']).
     */
    public function get(string $path, Closure $callback) {
        $this->add("GET", $path, $callback);
    }

    /**
     * Registers a POST route.
     *
     * @param string  $path     Route path.
     * @param Closure $callback Callback that receives route variables as an associative array.
     */
    public function post(string $path, Closure $callback) {
        $this->add("POST", $path, $callback);
    }

    /**
     * Registers a PUT route.
     *
     * @param string  $path     Route path.
     * @param Closure $callback Callback that receives route variables as an associative array.
     */
    public function put(string $path, Closure $callback) {
        $this->add("PUT", $path, $callback);
    }

    /**
     * Registers a DELETE route.
     *
     * @param string  $path     Route path.
     * @param Closure $callback Callback that receives route variables as an associative array.
     */
    public function delete(string $path, Closure $callback) {
        $this->add("DELETE", $path, $callback);
    }

    /**
     * Registers a PATCH route.
     *
     * @param string  $path     Route path.
     * @param Closure $callback Callback that receives route variables as an associative array.
     */
    public function patch(string $path, Closure $callback) {
        $this->add("PATCH", $path, $callback);
    }

    /**
     * Registers a OPTIONS route.
     *
     * @param string  $path     Route path.
     * @param Closure $callback Callback that receives route variables as an associative array.
     */
    public function options(string $path, Closure $callback) {
        $this->add("OPTIONS", $path, $callback);
    }

    /**
     * Adds a route to the router.
     *
     * Rules:
     * - {@code $method} and {@code $path} must not be empty; otherwise the request
     *   is ignored and logged as an error.
     * - An empty segment after splitting by '/' triggers an error and the request
     *   is ignored.
     * - Segments starting with {@see VARIABLE_PREFIX} are treated as variables.
     * - Already defined routes cannot be redefined; attempting to do so will be
     *   ignored and logged as an error.
     *
     * @param string  $method   HTTP method (e.g. "GET", "POST").
     * @param string  $path     Route path (e.g. "/users/:id").
     * @param Closure $callback Callback that will handle the matched route.
     */
    private function add(string $method, string $path, Closure $callback) {
        if (empty($method) || empty($path)) {
            return LogHelper::logError("Empty request method or path");
        }
        $seenVariables = []; // Tracking seen variables to prevent duplicates
        $node = &$this->routes;
        $route = Router::getRoute($method, $path);
        foreach(explode('/', $route) as $routeSegment) {
            if (empty($routeSegment)) {
                return LogHelper::logError("Empty route segment");
            }
            $isVariable = $routeSegment[0] === VARIABLE_PREFIX;
            $key = $isVariable ? substr($routeSegment, 1) : $routeSegment;
            $bucket = $isVariable ? "variables" : "children";
            if (in_array($key, $seenVariables, true)) {
                return LogHelper::logError("Duplicate variable name in route: '$key'");
            }
            if (!isset($node->{$bucket}[$key])) {
                $node->{$bucket}[$key] = new Node();
            }
            $node = &$node->{$bucket}[$key];
            $seenVariables[] = $key;
        }
        if ($node->isEnding) {
            return LogHelper::logError("Duplicate routes");
        }
        $node->isEnding = true;
        $node->callback = $callback;
    }

    /**
     * Dispatches a request to the matching route.
     *
     * Rules:
     * - {@code $method} and {@code $path} must not be empty; otherwise the request
     *   is ignored and logged as an error.
     * - If the route matches, the associated callback is invoked.
     * - Variables defined in the route (prefixed with {@see VARIABLE_PREFIX}) are
     *   passed to the callback as an associative array {@code $params}, where keys
     *   are variable names and values are the corresponding path segments.
     *
     * @param string $method HTTP method of the request (e.g. "GET").
     * @param string $path   Request path (e.g. "/users/123").
     */
    public function dispatch(string $method, string $path) {
        if (empty($method) || empty($path)) {
            return LogHelper::logError("Empty request method or path");
        }
        $route = Router::getRoute($method, $path);
        $segments = explode('/', $route);
        if (empty($segments)) {
            return LogHelper::logError("Empty route segments");
        }
        $result = $this->dispatchDFS($this->routes, $segments, 0);
        if ($result->found && isset($result->callback)) {
            call_user_func($result->callback, $result->params);
        } else {
            echo "404 Not Found\n";
        }
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
    
    private static function getRoute(string $method, string $path): string {
        $route = $method . $path;
        $route = trim($route); // Whitespaces
        $route = rtrim($route, '/'); // Trailing slashes
        return $route;
    }
}

