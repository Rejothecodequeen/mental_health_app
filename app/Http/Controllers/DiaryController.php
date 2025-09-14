<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiaryController extends Controller
{
    // List all entries for the logged-in user
    public function index()
    {
        return DiaryEntry::where('user_id', Auth::id())
            ->orderBy('entry_date', 'desc')
            ->get();
    }

    // Store a new entry
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'entry_date' => 'nullable|date'
        ]);

        $entry = DiaryEntry::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'entry_date' => $request->entry_date ?? now()->toDateString(),
        ]);

        return response()->json(['message' => 'Diary entry created', 'entry' => $entry]);
    }

    // Update an entry
    public function update(Request $request, $id)
    {
        $entry = DiaryEntry::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'entry_date' => 'nullable|date'
        ]);

        $entry->update($request->only('title', 'content', 'entry_date'));

        return response()->json(['message' => 'Diary entry updated', 'entry' => $entry]);
    }

    // Delete an entry
    public function destroy($id)
    {
        $entry = DiaryEntry::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $entry->delete();

        return response()->json(['message' => 'Diary entry deleted']);
    }
}
