<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\Src\SessionManager;
use GuiBranco\PocMvc\Src\Controller\BaseController;

class AuthController extends BaseController
{
    private $users;

    public function __construct($viewsPath)
    {
        parent::__construct($viewsPath);
        $this->users = [
            'john' => 'password123',
            'jane' => 'securePass',
        ];
    }

    public function login()
    {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        $errors = $this->validate($username, $password);
        if (!empty($errors)) {
            return $this->view('login', ['errors' => $errors]);
        }

        if ($this->authenticate($username, $password)) {
            SessionManager::set('user', $username);
            $this->redirect('/');
        } else {
            return $this->view('login', ['errors' => ['Invalid username or password']]);
        }
    }

    public function logout()
    {
        SessionManager::destroy();
        $this->redirect('/login');
    }

    private function validate($username, $password)
    {
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username is required';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        }

        return $errors;
    }

    private function authenticate($username, $password)
    {
        return isset($this->users[$username]) && $this->users[$username] === $password;
    }
}
