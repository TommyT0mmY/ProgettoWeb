<?php
declare(strict_types=1);

namespace Unibostu\Core\router\routes;

abstract class Route {
    public function __construct(
        public string $method,
        public string $path
    ) {}
}
