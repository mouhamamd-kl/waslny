<?php

namespace App\Channels\Messages;

class FirebaseMessage
{
    public $title;
    public $body;
    public $data = [];

    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    public function body($body)
    {
        $this->body = $body;
        return $this;
    }

    public function data($data)
    {
        $this->data = $data;
        return $this;
    }
}
