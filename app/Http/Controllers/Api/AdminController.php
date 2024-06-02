<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function home()
    {
        return response()->json([
            'message' => 'Welcome to the admin home!'
        ]);
    }
}
