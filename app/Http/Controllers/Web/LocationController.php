<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use App\Models\Subdistrict;
use App\Models\Village;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getProvinces()
    {
        return Province::orderBy('name')->get(['id', 'name']);
    }

    public function getCities(Request $request)
    {
        return City::where('province_id', $request->province_id)
                  ->orderBy('name')
                  ->get(['id', 'name']);
    }

    public function getSubdistricts(Request $request)
    {
        return Subdistrict::where('city_id', $request->city_id)
                         ->orderBy('name')
                         ->get(['id', 'name']);
    }

    public function getVillages(Request $request)
    {
        return Village::where('subdistrict_id', $request->subdistrict_id)
                     ->orderBy('name')
                     ->get(['id', 'name']);
    }
}