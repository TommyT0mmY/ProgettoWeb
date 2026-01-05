<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Response;
use Unibostu\Core\router\routes\Get;

class HomeController extends BaseController {
    
    #[Get('/')]
    public function index(): Response {
        return $this->render("home", []);
    }
}

