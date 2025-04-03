<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\Src\Controller\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return $this->view('index', [
            'title' => 'Home Page'
        ]);
    }

    public function docs()
    {
        return $this->view('docs', [
            'title' => 'Documentation'
        ]);
    }

    public function sandbox()
    {
        return $this->view('sandbox', [
            'title' => 'Sandbox'
        ]);
    }

    public function sections()
    {
        return $this->view('sections', [
            'title' => 'Sections'
        ]);
    }
}
