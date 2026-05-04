<?php

namespace App\Http\Controllers\Backend\Abdullah;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\Datatables\Datatables;

class FeatureController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $features = Feature::latest()->get();

            return DataTables::of($features)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return $row->title ?? '';
                })
                ->addColumn('description', function ($row) {
                    return strip_tags($row->description ?? '');
                })
                ->addColumn('icon', function ($row) {
                    if ($row->icon) {
                        $img = asset($row->icon);
                        return '<img src="' . $img . '" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">';
                    }
                    return '<span class="text-muted">No icon</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.feature.edit', $row->id);
                    $deleteUrl = route('admin.feature.destroy', $row->id);

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

        return view('backend.abdullah.feature.index');
    }

    public function create()
    {
        return view('backend.abdullah.feature.create');
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
            $iconPath = $request->file('icon')->store('uploads/features', 'public');
        }

        Feature::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'icon' => $iconPath,
        ]);

        return redirect()->route('admin.feature.index')->with('success', 'Feature created successfully.');
    }

    public function edit($id)
    {
        $feature = Feature::findOrFail($id);
        return view('backend.abdullah.feature.edit', compact('feature'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $feature = Feature::findOrFail($id);

        $iconPath = $feature->icon;
        if ($request->hasFile('icon')) {
            if ($iconPath && File::exists(public_path('storage/' . $iconPath))) {
                File::delete(public_path('storage/' . $iconPath));
            }
            $iconPath = $request->file('icon')->store('uploads/features', 'public');
        }
        $feature->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'icon' => $iconPath,
        ]);

        return redirect()->route('admin.feature.index')->with('success', 'Feature updated successfully.');
    }

    public function destroy($id)
    {
        $feature = Feature::findOrFail($id);

        if ($feature->icon && File::exists(public_path('storage/' . $feature->icon))) {
            File::delete(public_path('storage/' .   $feature->icon));
        }

        $feature->delete();

        return redirect()->route('admin.feature.index')->with('success', 'Feature deleted successfully.');
    }
}
