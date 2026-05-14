<?php

namespace App\Http\Controllers\Backend\Abdullah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Scan;
use Yajra\Datatables\Datatables;

class ScanController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $scans = Scan::latest()->get();

            return Datatables::of($scans)
                ->addIndexColumn()
            ->addColumn('title', function ($scan) {
                return $scan->title;
            })
            ->addColumn('description', function ($scan) {
                return $scan->description;
            })
            ->addColumn('price', function ($scan) {
                return $scan->price;
            })
            ->addColumn('items', function ($scan) {
                return $scan->items;
            })
            ->addColumn('action', function ($scan) {
                $editUrl = route('admin.scan.edit', $scan->id);
                $deleteUrl = route('admin.scan.destroy', $scan->id);
                return '<a href="' . $editUrl . '" class="btn btn-sm btn-primary">Edit</a>
                        <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                        </form>';
            })
            ->rawColumns(['title', 'description', 'price', 'items','action'])
            ->make(true);
        }
        return view('backend.abdullah.scan.index');
    }

    public function create()
    {
        return view('backend.abdullah.scan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'items' => 'nullable|array',
        ]);

        $scan = new Scan();
        $scan->title = $request->input('title');
        $scan->description = $request->input('description');
        $scan->price = $request->input('price');
        $scan->items = $request->input('items');
        $scan->save();

        return redirect()->route('admin.scan.index')->with('success', 'Scan created successfully.');
    }

    public function edit($id)
    {
        $scans = Scan::findOrFail($id);
        return view('backend.abdullah.scan.edit', compact('scans'));
    }
   

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'items' => 'nullable|array',
        ]);

        $scan = Scan::findOrFail($id);
        $scan->title = $request->input('title');
        $scan->description = $request->input('description');
        $scan->price = $request->input('price');
        $scan->items = $request->input('items');
        $scan->save();

        return redirect()->route('admin.scan.index')->with('success', 'Scan updated successfully.'); 


    }

    public function destroy($id)
    {
        $scans = Scan::findOrFail($id);
        $scans->delete();

        return redirect()->route('admin.scan.index')->with('success', 'Scan deleted successfully.');
    }

}

