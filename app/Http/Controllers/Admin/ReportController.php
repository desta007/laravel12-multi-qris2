<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Reports page']);
    }
    
    public function export()
    {
        return response()->json(['message' => 'Export reports']);
    }
}