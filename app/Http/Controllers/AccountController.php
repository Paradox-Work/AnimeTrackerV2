<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        return view('account.index', [
            'user' => $request->user(),
        ]);
    }
}
