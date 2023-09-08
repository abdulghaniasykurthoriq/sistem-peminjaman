<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private function history_read()
    {
        $history = history::all();

        if (!$history && Auth::user()->name)
        {
            return response()->json(
            [
                'status' => 'failed',
                'error_pesan' => 'TIDAK ADA HISTORY',
            ]);
        }

        return response()->json($history, 200);
    } 
}
