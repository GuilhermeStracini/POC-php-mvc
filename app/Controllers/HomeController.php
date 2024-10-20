<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\Src\Controller\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return $this->view('index', [
            'title' => 'Home Page',
            'message' => 'This is a simple home page loaded via the BaseController.'
        ]);
    }
}