<?php

namespace App\Http\Controllers\Backend\Abdullah;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FileFacade;
use Yajra\Datatables\Datatables;

class FileController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $files = File::latest()->get();

            return Datatables::of($files)
                ->addIndexColumn()
                ->addColumn('file', function ($row) {
                    if ($row->file) {
                        $url = asset('storage/' . $row->file);
                        return '<a href="' . $url . '" target="_blank">Download</a>';
                    }
                    return '<span class="text-muted">No file</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.file.edit', $row->id);
                    $deleteUrl = route('admin.file.destroy', $row->id);

                    return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary me-1">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger delete-button" onclick="return confirm(\'Are you sure?\')">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                    ';
                })
                ->rawColumns(['file','action'])
                ->make(true);
        }

        return view('backend.abdullah.file.index');
    }

    public function create()
    {
        return view('backend.abdullah.file.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('uploads/files', 'public');
        }

        File::create([
            'file' => $filePath,
        ]);

        return redirect()->route('admin.file.index')->with('success', 'File uploaded successfully.');
    }

    public function edit($id)
    {
        $file = File::findOrFail($id);
        return view('backend.abdullah.file.edit', compact('file'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'file' => 'nullable|file|max:10240',
        ]);

        $file = File::findOrFail($id);

        $filePath = $file->file;
        if ($request->hasFile('file')) {
            if ($filePath && FileFacade::exists(public_path('storage/' . $filePath))) {
                FileFacade::delete(public_path('storage/' . $filePath));
            }
            $filePath = $request->file('file')->store('uploads/files', 'public');
        }

        $file->update([
            'file' => $filePath,
        ]);

        return redirect()->route('admin.file.index')->with('success', 'File updated successfully.');
    }

    public function destroy($id)
    {
        $file = File::findOrFail($id);

        if ($file->file && FileFacade::exists(public_path('storage/' . $file->file))) {
            FileFacade::delete(public_path('storage/' . $file->file));
        }

        $file->delete();

        return redirect()->route('admin.file.index')->with('success', 'File deleted successfully.');
    }
}
