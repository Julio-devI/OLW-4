<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImpersonateController extends Controller
{
    public function impersonate($user_id)
    {
        $originalId = auth()->user()->id;
        session()->put('impersonate', $originalId);

        auth()->loginUsingId($user_id);

        return redirect()->route('clients.index');
    }
}
