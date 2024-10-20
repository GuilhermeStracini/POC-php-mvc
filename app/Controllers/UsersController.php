<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\Src\Controller\BaseController;

class UsersController extends BaseController
{
    private array $users = [];

    public function __construct(string $viewsPath)
    {
        parent::__construct($viewsPath);

        $this->users = [
            1 => ['name' => 'John Doe', 'email' => 'john.doe@example.com'],
            2 => ['name' => 'Jane Doe', 'email' => 'jane.doe@example.com'],
        ];
    }

    public function index()
    {
        return $this->view('index', ['title' => 'List of users', 'users' => $this->users], 'layout');
    }


    public function show(array $params)
    {
        $id = $params['id'] ?? 0;

        if (!array_key_exists($id, $this->users)) {
            return $this->view('error', ['title' => 'User not found'], 'layout');
        }

        return $this->view('show', ['title' => 'User details', 'user' => $this->users[$id]], 'layout');
    }
}