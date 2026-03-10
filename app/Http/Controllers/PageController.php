<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function products()
    {
        return view('pages.products');
    }

    public function solutions()
    {
        return view('pages.solutions');
    }

    public function pricing()
    {
        return view('pages.pricing');
    }

    public function about()
    {
        return view('pages.about');
    }
}
