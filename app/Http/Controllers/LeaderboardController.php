<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index()
    {
        $topPlayers = User::where('role', 'player')
            ->orderByDesc('score')
            ->take(20)
            ->get(['id', 'name', 'score']);

        return response()->json(['data' => $topPlayers]);
    }
}
