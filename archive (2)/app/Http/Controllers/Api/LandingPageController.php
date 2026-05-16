<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Feature;
use App\Models\Stat;
use App\Models\Work;
use App\Models\PackageAndSubscription;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function getLandingPageData()
    {
        try {
            $data = [
                'banner'   => Banner::latest()->get()->map(function ($item) {
                    // Banner icons/images calculation
                    $icons = is_string($item->icon) ? json_decode($item->icon, true) : $item->icon;
                    if (is_array($icons)) {
                        $item->icon = array_map(function ($iconName) {
                            return $iconName ? asset($iconName) : null;
                        }, $icons);
                    }

                    $item->icon_title = is_string($item->icon_title) ? json_decode($item->icon_title, true) : $item->icon_title;
                    return $item;
                }),

                'stats'    => Stat::latest()->get()->map(function ($item) {
                    // Stat migration has title, description, icon
                    $item->icon = $item->icon ? asset($item->icon) : null;
                    return $item;
                }),
                'features' => Feature::latest()->get()->map(function ($item) {
                    // Feature migration has title, description, icon
                    $item->icon = $item->icon ? asset($item->icon) : null;
                    return $item;
                }),
                'works'    => Work::orderBy('id', 'asc')->get()->map(function ($item) {
                    // Work migration has title, description, icon
                    $item->icon = $item->icon ? asset($item->icon) : null;
                    return $item;
                }),

                'system_settings' => \App\Models\SystemSetting::latest()->first()

            ];

            return response()->json([
                'success' => true,
                'message' => 'Landing page data retrieved successfully',
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBannerData(Request $request)
    {

        try {
            $data = Banner::latest()->first();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFeatureData(Request $request)
    {

        try {
            $data = Feature::query()->latest()->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
