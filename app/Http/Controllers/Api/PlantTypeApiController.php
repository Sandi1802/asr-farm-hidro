<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlantType;

class PlantTypeApiController extends Controller
{
    public function index()
    {
        try {
            $types = PlantType::all();
            
            return response()->json([
                'success' => true,
                'data' => $types
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat tipe tanaman: ' . $e->getMessage()
            ], 500);
        }
    }
}
