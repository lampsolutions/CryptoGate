<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Cache;

class LegalController extends Controller
{

    public function __construct()
    {
    }

    public function dsgvo(Request $request)
    {
        $data = Cache::get('dsgvo');

        return view('legal.dsgvo', ['text' => $data]);
    }

    public function impressum(Request $request)
    {
        $data = Cache::get('impressum');

        return view('legal.impressum', ['text' => $data]);
    }

    public function agb(Request $request)
    {
        $data = Cache::get('agb');

        return view('legal.agb', ['text' => $data]);
    }
}