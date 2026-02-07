<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Display user management page.
     */
    public function users()
    {
        // TODO: Implement user management
        return view('admin.users');
    }

    /**
     * Display system settings page.
     */
    public function settings()
    {
        // TODO: Implement system settings
        return view('admin.settings');
    }

    /**
     * Display reports page.
     */
    public function reports()
    {
        // TODO: Implement reports
        return view('admin.reports');
    }
}
