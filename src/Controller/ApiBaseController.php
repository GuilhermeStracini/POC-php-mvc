<?php

namespace GuiBranco\PocMvc\Src\Controller;

use GuiBranco\PocMvc\Src\Controller\BaseController;

class ApiBaseController extends BaseController
{
    public function __construct()
    {
        parent::__construct("./", null);
    }

    protected function view(string $viewName, array $data = [], ?string $layout = 'layout'): void
    {
        $this->json($data);
    }
}
