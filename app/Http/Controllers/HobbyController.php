<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use Illuminate\Http\Request;

class HobbyController extends Controller
{
    public function index()
    {
        $hobbies = Hobby::all();
        return view('hobbies.index', compact('hobbies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hobby' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        Hobby::create($request->all());

        return redirect()->back()->with('success', 'Hobby added successfully.');
    }
}
