<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class DefaultController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('default', ['title' => 'CryptoGate 2.0']);
    }
}
