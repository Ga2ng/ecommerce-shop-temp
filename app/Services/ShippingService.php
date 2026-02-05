<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShippingService
{
    protected $apiKey;
    protected $calculateApiKey;
    protected $baseUrl;
    protected $accountType;
    protected $komerceBaseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.api_key');
        $this->calculateApiKey = config('services.rajaongkir.calculate_api_key', $this->apiKey);
        $this->accountType = config('services.rajaongkir.account_type', 'starter');
        $this->komerceBaseUrl = config('services.rajaongkir.komerce_base_url', 'https://rajaongkir.komerce.id/api/v1');
        $this->baseUrl = $this->accountType === 'pro'
            ? 'https://pro.rajaongkir.com/api'
            : 'https://api.rajaongkir.com';
    }

    /**
     * Get shipping cost via Komerce domestic-cost API
     * POST .../calculate/domestic-cost
     * origin & destination = IDs from Search Domestics Destinations; weight in grams; courier code; price = lowest|highest
     * Response: data[] = { name, code, service, description, cost (int), etd (string) }
     */
    public function getCostDistrict(int $originDistrictId, int $destinationDistrictId, int $weight, string $courier): array
    {
        $allowed = ['jne', 'sicepat', 'ide', 'sap', 'jnt', 'ninja', 'tiki', 'lion', 'anteraja', 'pos', 'ncs', 'rex', 'rpx', 'sentral', 'star', 'wahana', 'dse'];
        if (!in_array(strtolower($courier), $allowed)) {
            return ['success' => false, 'message' => 'Kurir tidak didukung.'];
        }
        if ($weight < 1) {
            $weight = 1;
        }

        $cacheKey = "komerce_domestic_cost_{$originDistrictId}_{$destinationDistrictId}_{$weight}_{$courier}";
        return Cache::remember($cacheKey, 3600, function () use ($originDistrictId, $destinationDistrictId, $weight, $courier) {
            try {
                if (empty($this->calculateApiKey)) {
                    return ['success' => false, 'message' => 'API key calculate tidak di-set.'];
                }
                $response = Http::withHeaders([
                    'key' => $this->calculateApiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->asForm()->post("{$this->komerceBaseUrl}/calculate/domestic-cost", [
                    'origin' => $originDistrictId,
                    'destination' => $destinationDistrictId,
                    'weight' => $weight,
                    'courier' => strtolower($courier),
                    'price' => 'lowest',
                ]);

                $data = $response->json();
                if (!$response->successful()) {
                    Log::warning('Komerce domestic-cost API HTTP error', [
                        'status' => $response->status(),
                        'body' => $data,
                    ]);
                    return [
                        'success' => false,
                        'message' => $data['meta']['message'] ?? 'Gagal menghitung ongkir.',
                    ];
                }

                $code = $data['meta']['code'] ?? null;
                if ($code != 200) {
                    return [
                        'success' => false,
                        'message' => $data['meta']['message'] ?? 'Gagal menghitung ongkir.',
                    ];
                }

                $rawList = $data['data'] ?? [];
                if (!is_array($rawList)) {
                    return ['success' => false, 'message' => 'Format response tidak valid.'];
                }

                $results = $this->normalizeDomesticCostResponse($rawList, $courier);
                if (empty($results)) {
                    return [
                        'success' => false,
                        'message' => 'Tidak ada layanan pengiriman tersedia untuk kurir ini.',
                    ];
                }

                return [
                    'success' => true,
                    'data' => $results,
                ];
            } catch (\Exception $e) {
                Log::error('Komerce calculate Error: ' . $e->getMessage(), [
                    'origin' => $originDistrictId,
                    'destination' => $destinationDistrictId,
                    'courier' => $courier,
                ]);
                return [
                    'success' => false,
                    'message' => 'Gagal menghubungi layanan ongkir. Silakan coba lagi.',
                ];
            }
        });
    }

    /**
     * Normalize domestic-cost API response to format expected by frontend
     * API returns: data[] = { name, code, service, description, cost (int), etd (string) } per service
     * We output: [ { code, name, costs: [ { service, cost: [ { value, etd } ] } ] } ] (grouped by courier)
     */
    private function normalizeDomesticCostResponse(array $rawList, string $requestedCourier): array
    {
        $byCourier = [];
        foreach ($rawList as $item) {
            $code = $item['code'] ?? strtolower($requestedCourier);
            $name = $item['name'] ?? strtoupper($code);
            $serviceName = $item['service'] ?? $item['name'] ?? 'Service';
            $cost = isset($item['cost']) ? (int) $item['cost'] : 0;
            $etd = $item['etd'] ?? '-';
            if ($cost < 0) {
                continue;
            }
            if (!isset($byCourier[$code])) {
                $byCourier[$code] = ['code' => $code, 'name' => $name, 'costs' => []];
            }
            $byCourier[$code]['costs'][] = [
                'service' => $serviceName,
                'cost' => [['value' => $cost, 'etd' => $etd]],
            ];
        }
        return array_values($byCourier);
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
     * Get available couriers for Komerce district domestic-cost API
     */
    public function getAvailableCouriers(): array
    {
        $couriers = [
            ['code' => 'jne', 'name' => 'JNE'],
            ['code' => 'sicepat', 'name' => 'SiCepat'],
            ['code' => 'ide', 'name' => 'IDE'],
            ['code' => 'sap', 'name' => 'SAP'],
            ['code' => 'jnt', 'name' => 'J&T'],
            ['code' => 'ninja', 'name' => 'Ninja Express'],
            ['code' => 'tiki', 'name' => 'TIKI'],
            ['code' => 'lion', 'name' => 'Lion Parcel'],
            ['code' => 'anteraja', 'name' => 'Anteraja'],
            ['code' => 'pos', 'name' => 'POS Indonesia'],
            ['code' => 'ncs', 'name' => 'NCS'],
            ['code' => 'rex', 'name' => 'REX'],
            ['code' => 'rpx', 'name' => 'RPX'],
            ['code' => 'sentral', 'name' => 'Sentral Cargo'],
            ['code' => 'star', 'name' => 'Star Cargo'],
            ['code' => 'wahana', 'name' => 'Wahana'],
            ['code' => 'dse', 'name' => 'DSE'],
        ];
        return [
            'success' => true,
            'data' => $couriers,
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
