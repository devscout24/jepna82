<?php

namespace App\Http\Controllers\Backend\Abdullah;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\Datatables\Datatables;

class WorkController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $works = Work::latest()->get();

            return Datatables::of($works)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return $row->title ?? '';
                })
                ->addColumn('description', function ($row) {
                    return strip_tags($row->description ?? '');
                })
                ->addColumn('icon', function ($row) {
                    if ($row->icon) {
                        $img = asset('storage/' . $row->icon);
                        return '<img src="' . $img . '" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">';
                    }
                    return '<span class="text-muted">No icon</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.work.edit', $row->id);
                    $deleteUrl = route('admin.work.destroy', $row->id);

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
                ->rawColumns(['title', 'description', 'icon','action'])
                ->make(true);   
        }

        return view('backend.abdullah.work.index');
    }

    public function create()
    {
        return view('backend.abdullah.work.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('uploads/works', 'public');
        }

        Work::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'icon' => $iconPath,
        ]);

        return redirect()->route('admin.work.index')->with('success', 'Work created successfully.');
    }

    public function edit($id)
    {
        $work = Work::findOrFail($id);
        return view('backend.abdullah.work.edit', compact('work'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $work = Work::findOrFail($id);

        $iconPath = $work->icon;
        if ($request->hasFile('icon')) {
            if ($iconPath && File::exists(public_path('storage/' . $iconPath))) {
                File::delete(public_path('storage/' . $iconPath));
            }
            $iconPath = $request->file('icon')->store('uploads/works', 'public');
        }
        $work->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'icon' => $iconPath,
        ]);

        return redirect()->route('admin.work.index')->with('success', 'Work updated successfully.');
    }

    public function destroy($id)
    {
        $work = Work::findOrFail($id);

        if ($work->icon && File::exists(public_path('storage/' . $work->icon))) {
            File::delete(public_path('storage/' . $work->icon));
        }

        $work->delete();

        return redirect()->route('admin.work.index')->with('success', 'Work deleted successfully.');
    }
}
