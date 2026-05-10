<?php

namespace App\Http\Controllers\Backend\Abdullah;

use App\Http\Controllers\Controller;
use App\Models\PackageAndSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PackageAndSubscriptionController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = PackageAndSubscription::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', function ($row) {
                    return $row->title;
                })
                ->addColumn('price', function ($row) {
                    return $row->currency . ' ' . $row->final_price;
                })
                ->addColumn('package_type', function ($row) {
                    return ucwords(str_replace('_', ' ', $row->package_type));
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.packages.edit', $row->id);
                    $deleteUrl = route('admin.packages.destroy', $row->id);

                    return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary me-1">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger delete-button" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.abdullah.packages.index');
    }

    public function create()
    {
        return view('backend.abdullah.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'package_type' => 'required|string|in:free_preview,one_time_basic,one_time_pro,bulk,subscription',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'final_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'page_limit' => 'nullable|integer|min:0',
            'billing_cycle' => 'nullable|string|in:one_time,monthly,yearly,lifetime',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title) . '-' . time();
        $data['features'] = $request->features ? explode("\n", str_replace("\r", "", $request->features)) : [];

        PackageAndSubscription::create($data);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Package created successfully.'
            ]);
        }

        return redirect()->route('admin.packages.index')->with('success', 'Package created successfully.');
    }

    public function edit($id)
    {
        $package = PackageAndSubscription::findOrFail($id);
        return view('backend.abdullah.packages.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'package_type' => 'required|string|in:free_preview,one_time_basic,one_time_pro,bulk,subscription',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'final_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'page_limit' => 'nullable|integer|min:0',
            'billing_cycle' => 'nullable|string|in:one_time,monthly,yearly,lifetime',
        ]);

        $package = PackageAndSubscription::findOrFail($id);
        $data = $request->all();
        $data['features'] = $request->features ? explode("\n", str_replace("\r", "", $request->features)) : [];

        $package->update($data);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Package updated successfully.'
            ]);
        }

        return redirect()->route('admin.packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $package = PackageAndSubscription::findOrFail($id);
        $package->delete();

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Package deleted successfully.'
            ]);
        }

        return redirect()->route('admin.packages.index')->with('success', 'Package deleted successfully.');
    }
}
