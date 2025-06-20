<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use Illuminate\Support\Str;


class CandidateController extends Controller
{


    protected $imageDir = 'assets/images/candidate';
    /**
     * Display a listing of candidates.
     */
    public function index()
    {
        // Paginate 10 per page (adjust as needed)
        $candidates = Candidate::all();
        return view('admin.candidates.index', compact('candidates'));
    }

    /**
     * Show the form for creating a new candidate.
     */
    public function create()
    {
        return view('admin.candidates.create');
    }

    /**
     * Store a newly created candidate in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'party'  => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'image'  => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($file = $request->file('image')) {
            // ensure target dir exists
            $destPath = public_path($this->imageDir);
            if (! is_dir($destPath)) {
                mkdir($destPath, 0755, true);
            }

            // generate unique filename
            $filename = Str::random(8) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($destPath, $filename);
            $imagePath = $this->imageDir . '/' . $filename;
        }

        Candidate::create([
            'name'   => $request->name,
            'party'  => $request->party,
            'status' => $request->status,
            'image'  => $imagePath,
        ]);

        return redirect()->route('candidates.index')
            ->with('success', 'Candidate created successfully.');
    }
    /**
     * Show the form for editing the specified candidate.
     */
    public function edit(Candidate $candidate)
    {
        return view('admin.candidates.edit', compact('candidate'));
    }

    /**
     * Update the specified candidate in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'party'  => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'image'  => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // If there's a new upload, delete the old file and save the new one
        if ($file = $request->file('image')) {
            // 1) delete existing file
            if ($candidate->image && file_exists(public_path($candidate->image))) {
                unlink(public_path($candidate->image));
            }

            // 2) ensure target directory exists
            $destPath = public_path($this->imageDir);
            if (! is_dir($destPath)) {
                mkdir($destPath, 0755, true);
            }

            // 3) move new file
            $filename = Str::random(8)
                . '_'
                . time()
                . '.'
                . $file->getClientOriginalExtension();

            $file->move($destPath, $filename);

            // 4) update model attribute
            $candidate->image = $this->imageDir . '/' . $filename;
        }

        // update the rest of the attributes
        $candidate->name   = $request->name;
        $candidate->party  = $request->party;
        $candidate->status = $request->status;
        $candidate->save();

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidate updated successfully.');
    }


    /**
     * Remove the specified candidate from storage.
     */
    public function destroy(Candidate $candidate)
    {
        // delete image file
        if ($candidate->image && file_exists(public_path($candidate->image))) {
            @unlink(public_path($candidate->image));
        }

        $candidate->delete();

        return redirect()->route('candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }
}
