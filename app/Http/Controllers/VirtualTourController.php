<?php

namespace App\Http\Controllers;

use App\Models\VirtualTour;
use Illuminate\View\View;

class VirtualTourController extends Controller
{
    public function index(): View
    {
        $tours = VirtualTour::query()
            ->where('is_active', true)
            ->withCount('scenes')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('tours-360.index', compact('tours'));
    }

    public function show(VirtualTour $tour): View
    {
        abort_unless($tour->is_active, 404);
        $tour->load('scenes');

        return view('tours-360.show', compact('tour'));
    }
}
