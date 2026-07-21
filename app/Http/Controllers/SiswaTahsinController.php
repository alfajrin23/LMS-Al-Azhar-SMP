<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiswaTahsinController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'siswa_sd') {
            abort(403);
        }

        return redirect()->route('dashboard', ['tab' => 'tahsin']);
    }
}
