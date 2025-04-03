<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\Src\Controller\ApiBaseController;

class ApiController extends ApiBaseController
{
    public function index()
    {
        return $this->json($_SERVER);
    }
}
