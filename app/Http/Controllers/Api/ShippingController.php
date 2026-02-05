<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Calculate shipping cost via Komerce district domestic-cost
     * origin = origin district ID, destination = destination district ID
     */
    public function calculateCost(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|integer',
            'destination' => 'required|integer',
            'weight' => 'required|integer|min:1',
            'courier' => 'required|string|max:50',
        ]);

        $result = $this->shippingService->getCostDistrict(
            (int) $validated['origin'],
            (int) $validated['destination'],
            (int) $validated['weight'],
            $validated['courier']
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Get city ID from RajaOngkir Komerce.id
     * With Komerce.id API, city ID is already compatible
     */
    public function getCityId(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|integer',
        ]);

        // With Komerce.id API, city ID is already the same as RajaOngkir
        $cityId = $this->shippingService->getCityIdFromRajaOngkir(
            $validated['city_id'],
            null
        );

        if ($cityId) {
            return response()->json([
                'success' => true,
                'city_id' => $cityId,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'City not found',
        ], 404);
    }

    /**
     * Get available couriers from RajaOngkir based on account type
     */
    public function getCouriers()
    {
        $result = $this->shippingService->getAvailableCouriers();

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }
}
