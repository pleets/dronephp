<?php

namespace Master\Controller;

use Drone\Mvc\View;
use Drone\Mvc\AbstractController;

class Admin extends AbstractController
{
    public function index()
    {
        return ["message" => "Hello world!"];
    }

    public function withView()
    {
        return new View("withView", ["message" => "Hello world!"]);
    }
}