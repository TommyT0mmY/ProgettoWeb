<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;
use Unibostu\Model\DTO\PostDTO;

class HomeController extends BaseController {
    
    public function index(): Response {
        return $this->render("home", []);
    }
}

