<?php
declare(strict_types=1);

namespace Unibostu\Core\router\routes;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Put extends Route {
    public function __construct(string $path) {
        parent::__construct('PUT', $path);
    }
}
