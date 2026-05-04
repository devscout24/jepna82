<?php

namespace App\Http\Controllers\Backend\Abdullah;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $banners = Banner::latest()->get();

            return DataTables::of($banners)
                ->addIndexColumn()

                // TITLE
                ->addColumn('title', function ($row) {
                    return $row->title ?? '';
                })

                // DESCRIPTION (clean text)
                ->addColumn('description', function ($row) {
                    return strip_tags($row->description ?? '');
                })

                // BUTTON TEXT
                ->addColumn('button_text', function ($row) {
                    return $row->button_text ?? '';
                })

                // ICONS
                ->addColumn('icons', function ($row) {
                    $icons = is_array($row->icon) ? $row->icon : [];
                    $titles = is_array($row->icon_title) ? $row->icon_title : [];

                    if (empty($icons)) {
                        return '<span class="text-muted">No icons</span>';
                    }

                    $html = '<div class="d-flex flex-wrap gap-2">';

                    foreach ($icons as $index => $iconPath) {
                        $img = asset($iconPath);
                        $text = isset($titles[$index]) ? e($titles[$index]) : '';

                        $html .= '
                        <div class="text-center" style="width:60px;">
                            <img src="' . $img . '"
                                style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                            <small class="d-block text-muted" style="font-size:11px;">
                                ' . $text . '
                            </small>
                        </div>
                    ';
                    }

                    $html .= '</div>';

                    return $html;
                })

                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.banner.edit', $row->id);
                    $deleteUrl = route('admin.banner.destroy', $row->id);

                    return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary me-1">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                ';
                })

                // ACTION BUTTONS
                // IMPORTANT: allow HTML rendering
                ->rawColumns(['icons', 'action'])

                ->make(true);
        }

        return view('backend.abdullah.banner.index');
    }

    public function create()
    {
        return view('backend.abdullah.banner.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'icons.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'icon_texts.*' => 'nullable|string|max:255',
        ]);

        $iconPaths = [];
        $iconTitles = [];

        foreach ($request->file('icons', []) as $index => $iconFile) {
            if (!$iconFile) {
                continue;
            }

            $directory = 'uploads/banner-icons/';
            if (!File::exists(public_path($directory))) {
                File::makeDirectory(public_path($directory), 0755, true);
            }

            $fileName = time() . '_' . uniqid() . '.' . $iconFile->getClientOriginalExtension();
            $iconFile->move(public_path($directory), $fileName);
            $iconPaths[] = $directory . $fileName;
            $iconTitles[] = $validatedData['icon_texts'][$index] ?? '';
        }

        Banner::create([
            'title' => $validatedData['title'] ?? null,
            'description' => $validatedData['description'] ?? null,
            'button_text' => $validatedData['button_text'] ?? null,
            'icon' => $iconPaths,
            'icon_title' => $iconTitles,
        ]);

        return redirect()->route('admin.banner.index')->with('success', 'Banner created successfully.');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);

        return view('backend.abdullah.banner.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'icons.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'icon_texts.*' => 'nullable|string|max:255',
        ]);

        $newIcons = [];
        $newTitles = [];
        $uploadedIcons = $request->file('icons', []);
        $existingIcons = $request->input('existing_icons', []);
        $iconTexts = $validatedData['icon_texts'] ?? [];

        $maxCount = max(count($existingIcons), count($uploadedIcons), count($iconTexts));

        for ($index = 0; $index < $maxCount; $index++) {
            $iconFile = $uploadedIcons[$index] ?? null;
            $existingIcon = $existingIcons[$index] ?? null;
            $title = $iconTexts[$index] ?? '';

            if ($iconFile) {
                if ($existingIcon && File::exists(public_path($existingIcon))) {
                    File::delete(public_path($existingIcon));
                }

                $directory = 'uploads/banner-icons/';
                if (!File::exists(public_path($directory))) {
                    File::makeDirectory(public_path($directory), 0755, true);
                }

                $fileName = time() . '_' . uniqid() . '.' . $iconFile->getClientOriginalExtension();
                $iconFile->move(public_path($directory), $fileName);
                $newIcons[] = $directory . $fileName;
                $newTitles[] = $title;
                continue;
            }

            if (!empty($existingIcon)) {
                $newIcons[] = $existingIcon;
                $newTitles[] = $title;
            }
        }

        $banner->update([
            'title' => $validatedData['title'] ?? null,
            'description' => $validatedData['description'] ?? null,
            'button_text' => $validatedData['button_text'] ?? null,
            'icon' => $newIcons,
            'icon_title' => $newTitles,
        ]);

        return redirect()->route('admin.banner.index')->with('success', 'Banner updated successfully.');
    }

 
}
