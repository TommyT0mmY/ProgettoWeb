<?php
declare(strict_types=1);

namespace Unibostu\Core\Http;

interface RequestHandlerInterface {
    public function handle(Request $request): Response;
}
