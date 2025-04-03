<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\App\Models\ContactModel;
use GuiBranco\PocMvc\Src\Controller\BaseController;

class ContactController extends BaseController
{
    public function showForm()
    {
        return $this->view('index', ['title' => 'Contact Us'], 'layout');
    }

    public function handleFormSubmission()
    {
        $model = new ContactModel($_POST);

        if (!$model->isValid()) {
            return $this->view('index', [
                'title' => 'Error',
                'errors' => $model->getErrors(),
            ]);
        }

        return $this->view('index', [
            'title' => 'Success',
            'success' => "<p>Thank you, {$model->name}, for reaching out! We'll get back to you soon.</p>"
        ]);
    }
}
