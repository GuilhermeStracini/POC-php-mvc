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
}
