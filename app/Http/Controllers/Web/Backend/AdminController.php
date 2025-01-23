<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    
    public function index()
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return view('web.backend.layout.dashboard');
        }
        return redirect()->back()->with('t-error', 'Unauthorized access');
    }

}
