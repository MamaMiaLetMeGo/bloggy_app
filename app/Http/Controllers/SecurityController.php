<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function show()
    {
        return view('profile.security', [
            'user' => auth()->user(),
            // Add any security-related data you want to display
        ]);
    }
}
