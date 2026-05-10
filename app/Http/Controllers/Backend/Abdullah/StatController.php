<?php

namespace App\Http\Controllers\Backend\Abdullah;

use App\Http\Controllers\Controller;
use App\Models\Stat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StatController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $stats = Stat::latest()->get();

            return datatables()->of($stats)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return $row->title ?? '';
                })
                ->addColumn('description', function ($row) {
                    return $row->description ?? '';
                })
                ->addColumn('icon', function ($row) {
                    if ($row->icon) {
                        $img = asset($row->icon);
                        return '<img src="' . $img . '" alt="' . $row->title . '" width="40" height="40" style="object-fit:cover;border-radius:6px;">';
                    }
                    return '<span class="text-muted">No Icon</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.stat.edit', $row->id);
                    $deleteUrl = route('admin.stat.destroy', $row->id);

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

        return view('backend.abdullah.stat.index');
    }

    public function create()
    {
        return view('backend.abdullah.stat.create');
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
            $directory = 'uploads/stat-icons/';
            if (!File::exists(public_path($directory))) {
                File::makeDirectory(public_path($directory), 0755, true);
            }
            $fileName = time() . '_' . uniqid() . '.' . $request->file('icon')->getClientOriginalExtension();
            $request->file('icon')->move(public_path($directory), $fileName);
            $iconPath = $directory . $fileName;
        }

        Stat::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $iconPath,
        ]);

        return redirect()->route('admin.stat.index')->with('success', 'Stat created successfully.');
    }

    public function edit($id)
    {
        $stat = Stat::findOrFail($id);

        return view('backend.abdullah.stat.edit', compact('stat'));
    }

    public function update(Request $request, $id)
    {
        $stat = Stat::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $iconPath = $stat->icon;
        if ($request->hasFile('icon')) {
            if ($stat->icon && File::exists(public_path($stat->icon))) {
                File::delete(public_path($stat->icon));
            }

            $directory = 'uploads/stat-icons/';
            if (!File::exists(public_path($directory))) {
                File::makeDirectory(public_path($directory), 0755, true);
            }
            $fileName = time() . '_' . uniqid() . '.' . $request->file('icon')->getClientOriginalExtension();
            $request->file('icon')->move(public_path($directory), $fileName);
            $iconPath = $directory . $fileName;
        }

        $stat->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $iconPath,
        ]);

        return redirect()->route('admin.stat.index')->with('success', 'Stat updated successfully.');
    }

    public function destroy($id)
    {
        $stat = Stat::findOrFail($id);

        if ($stat->icon && File::exists(public_path($stat->icon))) {
            File::delete(public_path($stat->icon));
        }

        $stat->delete();

        return redirect()->back()->with('success', 'Stat deleted successfully.');
    }
}
