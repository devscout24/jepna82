<?php

namespace App\Http\Controllers\Backend\Abdullah;

use App\Http\Controllers\Controller;
use App\Models\SubscribePlan;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class SubscribePlanController extends Controller
{
    public function index(Request $request)
    {
       if($request->ajax()){
            $subscribePlans = SubscribePlan::latest()->get();

            return Datatables::of($subscribePlans)
                ->addIndexColumn()
                ->addColumn('title', function ($subscribePlan) {
                    return $subscribePlan->title;
                })
                ->addColumn('title_text', function ($subscribePlan) {
                    return $subscribePlan->title_text;
                })
                ->addColumn('price', function ($subscribePlan) {
                    return $subscribePlan->price;
                })
                ->addColumn('price_text', function ($subscribePlan) {
                    return $subscribePlan->price_text;
                })
                ->addColumn('items', function ($subscribePlan) {
                    return implode(', ', $subscribePlan->items);
                })
                ->addColumn('action', function ($subscribePlan) {
                    $editUrl = route('admin.subscribe_plan.edit', $subscribePlan->id);
                    $deleteUrl = route('admin.subscribe_plan.destroy', $subscribePlan->id);
                    return '<a href="' . $editUrl . '" class="btn btn-sm btn-primary">Edit</a>
                            <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                })
                ->rawColumns(['title', 'title_text', 'price', 'price_text', 'items','action'])
                ->make(true);
        }
        return view('backend.abdullah.subscribe_plan.index');
    }

    public function create()
    {
        return view('backend.abdullah.subscribe_plan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_text' => 'nullable|string',
            'price' => 'nullable|numeric',
            'price_text' => 'nullable|string',
            'items' => 'nullable|array',
        ]);

        $subscribePlan = new SubscribePlan();
        $subscribePlan->title = $request->input('title');
        $subscribePlan->title_text = $request->input('title_text');
        $subscribePlan->price = $request->input('price');
        $subscribePlan->price_text = $request->input('price_text');
        $subscribePlan->items = $request->input('items');
        $subscribePlan->save();

        return redirect()->route('admin.subscribe_plan.index')->with('success', 'Subscribe plan created successfully.');
    }

    public function edit($id)
    {
        $subscribePlan = SubscribePlan::findOrFail($id);

        return view('backend.abdullah.subscribe_plan.edit', compact('subscribePlan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_text' => 'nullable|string',
            'price' => 'nullable|numeric',
            'price_text' => 'nullable|string',
            'items' => 'nullable|array',
        ]);

        $subscribePlan = SubscribePlan::findOrFail($id);
        $subscribePlan->title = $request->input('title');
        $subscribePlan->title_text = $request->input('title_text');
        $subscribePlan->price = $request->input('price');
        $subscribePlan->price_text = $request->input('price_text');
        $subscribePlan->items = $request->input('items');
        $subscribePlan->save();

        return redirect()->route('admin.subscribe_plan.index')->with('success', 'Subscribe plan updated successfully.');
    }

    public function destroy($id)
    {
        $subscribePlan = SubscribePlan::findOrFail($id);
        $subscribePlan->delete();

        return redirect()->route('admin.subscribe_plan.index')->with('success', 'Subscribe plan deleted successfully.');
    }
}
