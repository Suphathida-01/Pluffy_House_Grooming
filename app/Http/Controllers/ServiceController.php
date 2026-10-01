<?php

namespace App\Http\Controllers;

use App\Services\ServiceCatalog;

class ServiceController extends Controller
{
    public function show(string $serviceSlug)
    {
        $example = ServiceCatalog::find($serviceSlug);

        if ($example === null) {
            abort(404);
        }

        $service = (object) $example;
        $durationLabel = $example['duration_label'];
        $priceTiers = array_map(function ($tier) use ($example) {
            return [
                'label' => $tier['size'],
                'weight' => $tier['weight'],
                'price' => $example['size_prices'][$tier['size']],
            ];
        }, ServiceCatalog::sizeLabels());

        return view('services.show', compact('service', 'durationLabel', 'serviceSlug', 'priceTiers'));
    }
}
