<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

class Controller extends BaseController
{
    /**
     * @return View
     */
    public function index(): View
    {
        return view('welcome');
    }
}
