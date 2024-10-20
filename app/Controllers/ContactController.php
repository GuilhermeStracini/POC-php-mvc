<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\App\Models\ContactModel;
use GuiBranco\PocMvc\Src\Controller\BaseController;

class ContactController extends BaseController
{
    public function index()
    {
        return $this->view('index', ['title' => 'Contact Us'], 'layout');
    }

    public function process()
    {
        $data = $_POST;

        $contact = new ContactModel($data);

        if (!$contact->isValid()) {
            return $this->view('process', [
                'title' => 'Error',
                'message' => 'Invalid contact data. Please provide a valid name, email and message.'
            ]);
        }

        return $this->view('process', [
            'title' => 'Success',
            'heading' => "Visitor {$contact->name} with email {$contact->email} sent a message!",
            'message' => $contact->message
        ]);
    }
}