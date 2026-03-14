<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class GeoController extends Controller
{
    /**
     * Verify user location against store radius
     */
    public function verifyLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        $userLat = $request->lat;
        $userLng = $request->lng;
        
        $storeLat = Config::get('geo.store_location.lat');
        $storeLng = Config::get('geo.store_location.lng');
        $maxRadius = Config::get('geo.max_radius_meters');

        // Haversine formula untuk hitung jarak (akurasi tinggi)
        $distance = $this->haversineDistance($storeLat, $storeLng, $userLat, $userLng);
        
        Log::info('Geo verification', [
            'user_id' => $request->user()?->id,
            'user_coords' => [$userLat, $userLng],
            'store_coords' => [$storeLat, $storeLng],
            'distance_m' => $distance,
            'max_radius' => $maxRadius
        ]);

        if ($distance > $maxRadius) {
            return response()->json([
                'success' => false,
                'message' => Config::get('geo.messages.out_of_radius')
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => Config::get('geo.messages.success'),
            'distance' => round($distance, 0) . 'm'
        ]);
    }

    /**
     * Hitung jarak dua titik GPS (Haversine Formula)
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLng/2) * sin($dLng/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}

