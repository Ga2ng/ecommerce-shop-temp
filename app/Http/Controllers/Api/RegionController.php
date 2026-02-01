<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RegionController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.api_key');
        $this->baseUrl = config('services.rajaongkir.komerce_base_url', 'https://rajaongkir.komerce.id/api/v1');
    }

    /**
     * Get all provinces from RajaOngkir Komerce.id
     */
    public function provinces(Request $request)
    {
        try {
            $search = $request->input('search', '');
            
            $cacheKey = 'rajaongkir_provinces';
            
            $provinces = Cache::remember($cacheKey, 86400, function () {
                $response = Http::withHeaders([
                    'key' => $this->apiKey,
                ])->get("{$this->baseUrl}/destination/province");
                $data = $response->json();
                
                Log::info('RajaOngkir Provinces API Response', [
                    'status' => $response->status(),
                    'data_structure' => array_keys($data ?? []),
                    'meta' => $data['meta'] ?? null,
                ]);
                
                if ($response->successful() && isset($data['meta']['code']) && $data['meta']['code'] == 200) {
                    return $data['data'] ?? [];
                }
                
                return [];
            });
            
            // Filter by search if provided
            if ($search) {
                $provinces = array_filter($provinces, function($province) use ($search) {
                    return stripos($province['name'], $search) !== false;
                });
            }
            
            // Sort by name
            usort($provinces, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            return response()->json([
                'data' => array_values($provinces)
            ]);
        } catch (\Exception $e) {
            Log::error('Provinces API Error: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'error' => 'Gagal memuat data provinsi.'
            ], 500);
        }
    }

    /**
     * Get cities by province from RajaOngkir Komerce.id
     */
    public function cities(Request $request, $provinceId)
    {
        try {
            $search = $request->input('search', '');
            
            $cacheKey = "rajaongkir_cities_province_{$provinceId}";
            
            $cities = Cache::remember($cacheKey, 86400, function () use ($provinceId) {
                $response = Http::withHeaders([
                    'key' => $this->apiKey,
                ])->get("{$this->baseUrl}/destination/city", [
                    'province_id' => $provinceId
                ]);
                $data = $response->json();
                
                Log::info('RajaOngkir Cities API Response', [
                    'status' => $response->status(),
                    'province_id' => $provinceId,
                    'data_structure' => array_keys($data ?? []),
                    'meta' => $data['meta'] ?? null,
                    'data_count' => isset($data['data']) ? count($data['data']) : 0,
                ]);
                
                if ($response->successful() && isset($data['meta']['code']) && $data['meta']['code'] == 200) {
                    return $data['data'] ?? [];
                }
                
                Log::warning('RajaOngkir Cities API returned empty or error', [
                    'status' => $response->status(),
                    'province_id' => $provinceId,
                    'response' => $data,
                ]);
                
                return [];
            });
            
            // Filter by search if provided
            if ($search) {
                $cities = array_filter($cities, function($city) use ($search) {
                    return stripos($city['name'], $search) !== false;
                });
            }
            
            // Sort by name
            usort($cities, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            return response()->json([
                'data' => array_values($cities)
            ]);
        } catch (\Exception $e) {
            Log::error('Cities API Error: ' . $e->getMessage(), [
                'province_id' => $provinceId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'data' => [],
                'error' => 'Gagal memuat data kota.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get districts by city from RajaOngkir Komerce.id
     */
    public function districts(Request $request, $cityId)
    {
        try {
            $search = $request->input('search', '');
            
            $cacheKey = "rajaongkir_districts_city_{$cityId}";
            
            $districts = Cache::remember($cacheKey, 86400, function () use ($cityId) {
                $response = Http::withHeaders([
                    'key' => $this->apiKey,
                ])->get("{$this->baseUrl}/destination/district", [
                    'city_id' => $cityId
                ]);
                $data = $response->json();
                
                if ($response->successful() && isset($data['meta']['code']) && $data['meta']['code'] == 200) {
                    return $data['data'] ?? [];
                }
                
                return [];
            });
            
            // Filter by search if provided
            if ($search) {
                $districts = array_filter($districts, function($district) use ($search) {
                    return stripos($district['name'], $search) !== false;
                });
            }
            
            // Sort by name
            usort($districts, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            return response()->json([
                'data' => array_values($districts)
            ]);
        } catch (\Exception $e) {
            Log::error('Districts API Error: ' . $e->getMessage(), [
                'city_id' => $cityId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'data' => [],
                'error' => 'Gagal memuat data kecamatan.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get villages by district from RajaOngkir Komerce.id
     * Note: API mungkin tidak punya endpoint untuk village, return empty untuk sekarang
     */
    public function villages(Request $request, $districtId)
    {
        try {
            // RajaOngkir Komerce.id mungkin tidak punya endpoint untuk village
            // Return empty array untuk sekarang
            return response()->json([
                'data' => []
            ]);
        } catch (\Exception $e) {
            Log::error('Villages API Error: ' . $e->getMessage(), [
                'district_id' => $districtId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'data' => [],
                'error' => 'Gagal memuat data kelurahan.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
