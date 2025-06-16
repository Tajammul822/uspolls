<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pollster;

class PollsterController extends Controller
{
    public function index()
    {
        $pollsters = Pollster::all();
        return view('admin.pollsters.index', compact('pollsters'));
    }

    public function create()
    {
        return view('admin.pollsters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|unique:pollsters,name',
            'rank'        => 'required|in:A+,A,B,C',
            'description' => 'nullable|string',
            'website'     => 'nullable|url',
        ]);

        Pollster::create($validated);

        return redirect()
            ->route('pollsters.index')
            ->with('success', 'Pollster created successfully.');
    }

    public function edit(Pollster $pollster)
    {
        return view('admin.pollsters.edit', compact('pollster'));
    }

    public function update(Request $request, Pollster $pollster)
    {
        $validated = $request->validate([
            'name'        => 'required|string|unique:pollsters,name,' . $pollster->id,
            'rank'        => 'required|in:A+,A,B,C',
            'description' => 'nullable|string',
            'website'     => 'nullable|url',
        ]);

        $pollster->update($validated);

        return redirect()
            ->route('pollsters.index')
            ->with('success', 'Pollster updated successfully.');
    }

    public function destroy(Pollster $pollster)
    {
        $pollster->delete();

        return redirect()
            ->route('pollsters.index')
            ->with('success', 'Pollster deleted successfully.');
    }
}
