<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function allData()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function promoteAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->update(['role' => 'admin']);
        return response()->json(['message' => "{$user->name} promoted to admin"]);
    }
}
