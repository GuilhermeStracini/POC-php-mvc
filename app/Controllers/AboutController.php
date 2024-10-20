<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\Src\Controller\BaseController;

class AboutController extends BaseController
{
    public function index()
    {
        return $this->view('index');
    }
}