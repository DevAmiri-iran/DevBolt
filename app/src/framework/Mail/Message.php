<?php

namespace App\Mail;

class Message
{
    public $to;
    public $subject;
    public $body;
    public $altBody;
    public $isHtml = true;
    public $view;
    public $viewData = [];
    public $attachments = [];

    public function to($address, $name = '')
    {
        $this->to = ['address' => $address, 'name' => $name];
        return $this;
    }

    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function body($body)
    {
        $this->body = $body;
        return $this;
    }

    public function altBody($altBody)
    {
        $this->altBody = $altBody;
        return $this;
    }

    public function isHtml($isHtml = true)
    {
        $this->isHtml = $isHtml;
        return $this;
    }

    public function view($view, $data = [])
    {
        $this->view = $view;
        $this->viewData = $data;
        return $this;
    }

    public function attach($filePath)
    {
        $this->attachments[] = $filePath;
        return $this;
    }
}