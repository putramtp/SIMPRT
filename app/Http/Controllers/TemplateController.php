<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::where('created_by', Auth::id())->latest()->get();
        return view('template.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'fields' => 'required|array',
        ]);
        $validated['created_by'] = Auth::id();
        $template = Template::create($validated);
        return response()->json(['success' => true, 'id' => $template->id, 'name' => $template->name]);
    }

    public function show(Template $template)
    {
        return response()->json($template);
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return response()->json(['success' => true]);
    }
}
