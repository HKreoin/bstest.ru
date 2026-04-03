<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class LegalController extends Controller
{
    public function privacy(): View
    {
        return view('legal.privacy');
    }

    public function cookies(): View
    {
        return view('legal.cookies');
    }
}
