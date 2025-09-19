<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    // View all resources (for Blade page)
    public function index()
    {
        $resources = Resource::latest()->get();
        return view('resources.index', compact('resources')); // pass to Blade
    }

    // Upload new resource (therapists only)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,mp4,jpg,png|max:10240',
            'url' => 'nullable|url'
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('resources', 'public');
        }

        $resource = Resource::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'file_path' => $filePath,
            'url' => $request->url,
            'uploaded_by' => Auth::id(),
        ]);

        // Redirect back to resources page instead of returning JSON
        return redirect()->route('resources.index')->with('success', 'Resource uploaded successfully!');
    }

    // Delete resource (therapist only)
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);

        if ($resource->uploaded_by != Auth::id()) {
            return redirect()->route('resources.index')->with('error', 'Unauthorized');
        }

        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()->route('resources.index')->with('success', 'Resource deleted successfully!');
    }

    // Optional: API endpoint for JSON responses (if needed)
    public function apiIndex()
    {
        return response()->json(Resource::latest()->get());
    }
}
