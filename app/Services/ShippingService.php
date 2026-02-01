<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShippingService
{
    protected $apiKey;
    protected $baseUrl;
    protected $accountType;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.api_key');
        $this->accountType = config('services.rajaongkir.account_type', 'starter'); // starter, basic, pro
        $this->baseUrl = $this->accountType === 'pro' 
            ? 'https://pro.rajaongkir.com/api'
            : 'https://api.rajaongkir.com';
    }

    /**
     * Get shipping cost from RajaOngkir
     * According to RajaOngkir documentation:
     * - origin: city ID (integer)
     * - destination: city ID (integer)
     * - weight: weight in gram (integer)
     * - courier: jne, pos, tiki, etc (string)
     */
    public function getCost(int $origin, int $destination, int $weight, string $courier): array
    {
        // Validate courier
        $allowedCouriers = ['jne', 'pos', 'tiki', 'rpx', 'pandu', 'wahana', 'sicepat', 'jnt', 'pahala', 'sap', 'jet', 'indah', 'dse', 'slis', 'first', 'ncs', 'star', 'ninja', 'lion', 'idl', 'rex', 'ide', 'sentral'];
        if (!in_array(strtolower($courier), $allowedCouriers)) {
            return [
                'success' => false,
                'message' => 'Kurir tidak didukung.',
            ];
        }

        // Minimum weight is 1 gram
        if ($weight < 1) {
            $weight = 1;
        }

        $cacheKey = "shipping_cost_{$origin}_{$destination}_{$weight}_{$courier}";
        
        return Cache::remember($cacheKey, 3600, function () use ($origin, $destination, $weight, $courier) {
            try {
                $response = Http::withHeaders([
                    'key' => $this->apiKey,
                ])->asForm()->post("{$this->baseUrl}/cost", [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => strtolower($courier),
                ]);

                $data = $response->json();

                // Check response structure according to RajaOngkir documentation
                if ($response->successful() && isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200) {
                    if (isset($data['rajaongkir']['results']) && !empty($data['rajaongkir']['results'])) {
                        return [
                            'success' => true,
                            'data' => $data['rajaongkir']['results'],
                            'origin_details' => $data['rajaongkir']['origin_details'] ?? null,
                            'destination_details' => $data['rajaongkir']['destination_details'] ?? null,
                        ];
                    }
                }

                // Handle error response
                $errorMessage = $data['rajaongkir']['status']['description'] ?? 'Failed to get shipping cost';
                
                Log::warning('RajaOngkir API Warning', [
                    'response' => $data,
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courier,
                ]);

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'code' => $data['rajaongkir']['status']['code'] ?? null,
                ];

            } catch (\Exception $e) {
                Log::error('RajaOngkir API Error: ' . $e->getMessage(), [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courier,
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to connect to shipping service. Please try again.',
                ];
            }
        });
    }

    /**
     * Get city ID from RajaOngkir Komerce.id by city ID
     * Since we're using Komerce.id API, city ID is already the same
     */
    public function getCityIdFromRajaOngkir(int $cityId, ?int $provinceId = null): ?int
    {
        // With Komerce.id API, city ID is already compatible with RajaOngkir
        // Just return the city ID directly
        return $cityId;
    }

    /**
     * Get provinces from RajaOngkir
     */
    public function getProvinces(): array
    {
        $cacheKey = 'rajaongkir_provinces';
        
        return Cache::remember($cacheKey, 86400, function () {
            try {
                $response = Http::withHeaders([
                    'key' => $this->apiKey,
                ])->get("{$this->baseUrl}/province");

                $data = $response->json();

                if ($response->successful() && isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200) {
                    return [
                        'success' => true,
                        'data' => $data['rajaongkir']['results'] ?? [],
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Failed to get provinces',
                ];

            } catch (\Exception $e) {
                Log::error('RajaOngkir Provinces Error: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Failed to connect to shipping service',
                ];
            }
        });
    }

    /**
     * Get available couriers based on account type
     * According to RajaOngkir documentation, couriers vary by account type
     */
    public function getAvailableCouriers(): array
    {
        // Based on RajaOngkir account types
        $couriers = [
            'starter' => [
                ['code' => 'jne', 'name' => 'JNE'],
                ['code' => 'pos', 'name' => 'POS Indonesia'],
                ['code' => 'tiki', 'name' => 'TIKI'],
            ],
            'basic' => [
                ['code' => 'jne', 'name' => 'JNE'],
                ['code' => 'pos', 'name' => 'POS Indonesia'],
                ['code' => 'tiki', 'name' => 'TIKI'],
                ['code' => 'rpx', 'name' => 'RPX'],
                ['code' => 'pandu', 'name' => 'Pandu'],
                ['code' => 'wahana', 'name' => 'Wahana'],
                ['code' => 'sicepat', 'name' => 'SiCepat'],
                ['code' => 'jnt', 'name' => 'J&T'],
                ['code' => 'pahala', 'name' => 'Pahala'],
                ['code' => 'sap', 'name' => 'SAP'],
                ['code' => 'jet', 'name' => 'JET'],
                ['code' => 'indah', 'name' => 'Indah Logistic'],
                ['code' => 'dse', 'name' => 'DSE'],
                ['code' => 'slis', 'name' => 'SLIS'],
                ['code' => 'first', 'name' => 'First Logistics'],
                ['code' => 'ncs', 'name' => 'NCS'],
                ['code' => 'star', 'name' => 'Star Cargo'],
                ['code' => 'ninja', 'name' => 'Ninja Express'],
                ['code' => 'lion', 'name' => 'Lion Parcel'],
                ['code' => 'idl', 'name' => 'IDL'],
                ['code' => 'rex', 'name' => 'REX'],
                ['code' => 'ide', 'name' => 'IDE'],
                ['code' => 'sentral', 'name' => 'Sentral Cargo'],
            ],
            'pro' => [
                ['code' => 'jne', 'name' => 'JNE'],
                ['code' => 'pos', 'name' => 'POS Indonesia'],
                ['code' => 'tiki', 'name' => 'TIKI'],
                ['code' => 'rpx', 'name' => 'RPX'],
                ['code' => 'pandu', 'name' => 'Pandu'],
                ['code' => 'wahana', 'name' => 'Wahana'],
                ['code' => 'sicepat', 'name' => 'SiCepat'],
                ['code' => 'jnt', 'name' => 'J&T'],
                ['code' => 'pahala', 'name' => 'Pahala'],
                ['code' => 'sap', 'name' => 'SAP'],
                ['code' => 'jet', 'name' => 'JET'],
                ['code' => 'indah', 'name' => 'Indah Logistic'],
                ['code' => 'dse', 'name' => 'DSE'],
                ['code' => 'slis', 'name' => 'SLIS'],
                ['code' => 'first', 'name' => 'First Logistics'],
                ['code' => 'ncs', 'name' => 'NCS'],
                ['code' => 'star', 'name' => 'Star Cargo'],
                ['code' => 'ninja', 'name' => 'Ninja Express'],
                ['code' => 'lion', 'name' => 'Lion Parcel'],
                ['code' => 'idl', 'name' => 'IDL'],
                ['code' => 'rex', 'name' => 'REX'],
                ['code' => 'ide', 'name' => 'IDE'],
                ['code' => 'sentral', 'name' => 'Sentral Cargo'],
            ],
        ];

        $accountType = strtolower($this->accountType);
        
        return [
            'success' => true,
            'data' => $couriers[$accountType] ?? $couriers['starter'],
        ];
    }

    /**
     * Get cities by province from RajaOngkir
     */
public function getCities(?int $provinceId = null): array
    {
        $cacheKey = $provinceId ? "rajaongkir_cities_{$provinceId}" : 'rajaongkir_cities_all';
        
        return Cache::remember($cacheKey, 86400, function () use ($provinceId) {
            try {
                $params = [];
                if ($provinceId) {
                    $params['province'] = $provinceId;
                }

                $response = Http::withHeaders([
                    'key' => $this->apiKey,
                ])->get("{$this->baseUrl}/city", $params);

                $data = $response->json();

                if ($response->successful() && isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] == 200) {
                    return [
                        'success' => true,
                        'data' => $data['rajaongkir']['results'] ?? [],
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Failed to get cities',
                ];

            } catch (\Exception $e) {
                Log::error('RajaOngkir Cities Error: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Failed to connect to shipping service',
                ];
            }
        });
    }
}
