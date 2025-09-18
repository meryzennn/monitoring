<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Data_kendala extends BaseController
{
    public function data_kendala()
    {

        $data = [

            'title'           => 'Data Kendala',
            'activeMenu'      => 'data-kendala',
        ];

        return view('Admin/data-kendala/data_kendala', $data);
    }
}
