<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $page = \App\Models\Page::where('slug', 'contact')->first();
        return Inertia::render('Contact/Index', [
            'page' => $page,
        ]);
    }
}
