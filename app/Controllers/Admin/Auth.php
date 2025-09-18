<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {

        return view('Admin/auth/login', ['title' => 'Login']);
    }
}
