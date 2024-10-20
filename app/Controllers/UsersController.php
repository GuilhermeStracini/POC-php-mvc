<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\App\Models\UserModel;
use GuiBranco\PocMvc\Src\Controller\BaseController;

class UsersController extends BaseController
{
    public function index()
    {
        return $this->view('index', ['title' => 'List of users', 'users' => UserModel::all()], 'layout');
    }

    public function show(array $params)
    {
        $id = $params['id'] ?? 0;

        if (!array_key_exists($id, UserModel::all())) {
            return $this->view('error', ['title' => 'User not found'], 'layout');
        }

        return $this->view('show', ['title' => 'User details', 'user' => UserModel::find($id)], 'layout');
    }
}
