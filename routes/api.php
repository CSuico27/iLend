<?php

use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/api/regions', [ApiController::class, 'getRegions'])->name('api.regions.index');
Route::get('/api/provinces', [ApiController::class, 'getProvinces'])->name('api.provinces.index');
Route::get('/api/municipalities', [ApiController::class, 'getMunicipalities'])->name('api.municipalities.index');
Route::get('/api/barangays', [ApiController::class, 'getBarangays'])->name('api.barangays.index');

Route::get('/location/province/{regionCode}', [ApiController::class, 'getProvincesByRegion'])->name('location.province');
Route::get('/location/municipalities/{provinceCode}', [ApiController::class, 'getMunicipalitiesByProvince'])->name('location.municipality');
Route::get('/location/barangay/{municipalityCode}', [ApiController::class, 'getBarangaysByMunicipality'])->name('location.barangay');