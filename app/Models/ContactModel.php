<?php

namespace GuiBranco\PocMvc\App\Models;

class ContactModel
{
    public string $name;
    public string $email;

    public string $message;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->message = $data['message'] ?? '';
    }

    public function isValid(): bool
    {
        return !empty($this->name) &&filter_var($this->email, FILTER_VALIDATE_EMAIL) && !empty($this->message);
    }

    public function getErrors(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors['name'] = 'Name is required';
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is invalid';
        }

        if (empty($this->message)) {
            $errors['message'] = 'Message is required';
        }

        return $errors;
    }
}
