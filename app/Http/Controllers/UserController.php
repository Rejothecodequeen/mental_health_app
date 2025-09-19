<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Search for users to start a chat with.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        // Don’t allow empty searches
        if (!$query) {
            return response()->json([]);
        }

        $users = User::where('id', '!=', Auth::id()) // exclude current user
            ->where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
