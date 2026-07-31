<?php

namespace App\Http\Controllers;

use App\Models\AttractionPlace;
use Illuminate\View\View;

class PublicPlaceController extends Controller
{
    public function show(AttractionPlace $place): View
    {
        abort_unless($place->activo, 404);
        abort_if($place->tourism_service_provider_id && $place->serviceProvider?->status !== 'approved', 404);
        $place->load('type.parent', 'serviceProvider.offerings');
        return view('mapa-interactivo.show', compact('place'));
    }
}
