<?php

namespace App\Controllers;

use App\Models\User_model;

class SessionController extends BaseController
{
    public function index() {
        echo view("session_expired");
    }
}