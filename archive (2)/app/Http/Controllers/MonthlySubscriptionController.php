<?php

namespace App\Http\Controllers;

use App\Models\MonthlySubscription;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class MonthlySubscriptionController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $monthlySubscriptions = MonthlySubscription::latest()->get();

            return datatables()->of($monthlySubscriptions)
                ->addIndexColumn()
                ->addColumn('title', function ($monthlySubscription) {
                    return $monthlySubscription->title;
                })
                ->addColumn('title_text', function ($monthlySubscription) {
                    return $monthlySubscription->title_text;
                })
                ->addColumn('price', function ($monthlySubscription) {
                    return $monthlySubscription->price;
                })
                ->addColumn('plan_id', function ($monthlySubscription) {
                    return $monthlySubscription->plan_id;
                })
                ->addColumn('stripe_price_id', function ($monthlySubscription) {
                    return $monthlySubscription->stripe_price_id;
                })
                ->addColumn('duration', function ($monthlySubscription) {
                    return $monthlySubscription->duration;
                })
                ->addColumn('action', function ($monthlySubscription) {
                    $editUrl = route('admin.monthly_plan.edit', $monthlySubscription->id);
                    $deleteUrl = route('admin.monthly_plan.destroy', $monthlySubscription->id);

                    return '<a href="' . $editUrl . '" class="btn btn-sm btn-primary">Edit</a>
                            <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                })
                ->rawColumns(['title', 'title_text', 'price', 'duration', 'plan_id', 'stripe_price_id', 'action'])
                ->make(true);
        }

        return view('backend.abdullah.monthly_plan.index');
    }

    public function create()
    {
        return view('backend.abdullah.monthly_plan.create');
    }

    public function store(Request $request)
    {
        // Validate and store the monthly subscription data
        $request->validate([
            'title' => 'required|string|max:255',
            'title_text' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'duration' => 'nullable|string|max:255',
            'plan_id' => 'nullable|string|max:255',
            'stripe_price_id' => 'nullable|string|max:255',
        ]);

            $monthlySubscription = new MonthlySubscription();
            $monthlySubscription->title = $request->input('title');
            $monthlySubscription->title_text = $request->input('title_text');
            $monthlySubscription->price = $request->input('price');
            $monthlySubscription->duration = $request->input('duration');
            $monthlySubscription->plan_id = $request->input('plan_id');
            $monthlySubscription->stripe_price_id = $request->input('stripe_price_id');
            $monthlySubscription->save();

        return redirect()->route('admin.monthly_plan.index')->with('success', 'Monthly subscription created successfully.');
    }


    public function edit($id)
    {
        $monthlySubscription = MonthlySubscription::findOrFail($id);
        return view('backend.abdullah.monthly_plan.edit', compact('monthlySubscription'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_text' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'duration' => 'nullable|string|max:255',
            'plan_id' => 'nullable|string|max:255',
            'stripe_price_id' => 'nullable|string|max:255',
        ]);

        $monthlySubscription = MonthlySubscription::findOrFail($id);
        $monthlySubscription->title = $request->input('title');
        $monthlySubscription->title_text = $request->input('title_text');
        $monthlySubscription->price = $request->input('price');
        $monthlySubscription->duration = $request->input('duration');
        $monthlySubscription->plan_id = $request->input('plan_id');
        $monthlySubscription->stripe_price_id = $request->input('stripe_price_id');
        $monthlySubscription->save();

        return redirect()->route('admin.monthly_plan.index')->with('success', 'Monthly subscription updated successfully.');
    }

    public function destroy($id)
    {
        $monthlySubscription = MonthlySubscription::findOrFail($id);
        $monthlySubscription->delete();

        return redirect()->route('admin.monthly_plan.index')->with('success', 'Monthly subscription deleted successfully.');
    }

}
