<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackageAndSubscription;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function subscriptionList(Request $request){
        try {
        $data = PackageAndSubscription::where('package_type', 'subscription')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Package list fetched successfully.',
            'data' => $data,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch package list',
        ], 500);
    }

    }

    public function creditPlansList(Request $request){
        try {
        $data = PackageAndSubscription::where('package_type', 'bulk')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Package list fetched successfully.',
            'data' => $data,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch package list',
        ], 500);
    }

    }

    public function basicPlansList(Request $request){
        try {
        $data = PackageAndSubscription::where('package_type', 'one_time_pro')->orWhere('package_type', 'one_time_basic')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Package list fetched successfully.',
            'data' => $data,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch package list',
        ], 500);
    }

    }

    public function freePlanList(Request $request){
        try {
        $data = PackageAndSubscription::where('package_type', 'free_preview')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Package list fetched successfully.',
            'data' => $data,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch package list',
        ], 500);
    }

    }
}
