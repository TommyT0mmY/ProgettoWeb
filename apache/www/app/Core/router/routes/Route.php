<?php
declare(strict_types=1);

namespace Unibostu\Core\router\routes;

/**
 * Base attribute for HTTP route definitions.
 * Subclassed by Get, Post, Put, Delete.
 */
abstract class Route {
    public function __construct(
        public string $method,
        public string $path
    ) {}
}
