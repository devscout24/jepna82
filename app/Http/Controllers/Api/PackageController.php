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
}
