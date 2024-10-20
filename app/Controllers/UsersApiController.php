<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\App\Models\UserModel;
use GuiBranco\PocMvc\Src\Controller\ApiBaseController;

class UsersApiController extends ApiBaseController
{
    public function index()
    {
        $users = UserModel::all(); // Assume User::all() fetches all users
        $this->jsonResponse($users);
    }

    public function show($params)
    {
        $user = UserModel::find($params['id']); // Assume User::find() fetches a user by ID
        if ($user) {
            $this->jsonResponse($user);
        } else {
            $this->jsonResponse(['message' => 'User not found'], 404);
        }
    }

    public function create()
    {
        // Handle creating a user
        $data = json_decode(file_get_contents('php://input'), true);
        $user = UserModel::create($data);
        $this->jsonResponse($user, 201);
    }

    public function update($params)
    {
        // Handle updating a user
        $data = json_decode(file_get_contents('php://input'), true);
        $user = UserModel::find($params['id']);
        if ($user) {
            UserModel::update($params['id'], $data); // Assume update updates the user data
            $this->jsonResponse($user);
        } else {
            $this->jsonResponse(['message' => 'User not found'], 404);
        }
    }

    public function delete($params)
    {
        $user = UserModel::find($params['id']);
        if ($user) {
            UserModel::delete($params['id']); // Assume delete removes the user
            $this->jsonResponse(['message' => 'User deleted']);
        } else {
            $this->jsonResponse(['message' => 'User not found'], 404);
        }
    }

    protected function jsonResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
