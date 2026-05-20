<?php

namespace App\Http\Controllers;

use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalUsers' => User::count(),
        ]);
    }
}
