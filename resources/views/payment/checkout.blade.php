@extends('layouts.catalog')

@section('title', 'Checkout')

@section('content')
<div class="min-h-screen bg-white dark:bg-black py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-black dark:text-white mb-8">Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('payment.process') }}" method="POST" class="space-y-6" id="checkout-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="{{ $quantity }}">
                    <input type="hidden" name="product_weight" value="1000" id="product_weight"> <!-- Default 1kg, bisa diubah -->

                    <!-- Customer Information -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-black dark:text-white mb-6">Customer Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap *</label>
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', auth()->check() ? auth()->user()->name : '') }}" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom dark:bg-gray-800 dark:text-white">
                                @error('customer_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                                <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', auth()->check() ? auth()->user()->email : '') }}" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom dark:bg-gray-800 dark:text-white">
                                @error('customer_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nomor HP *</label>
                                <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom dark:bg-gray-800 dark:text-white">
                                @error('customer_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-black dark:text-white mb-6">Shipping Address</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alamat Lengkap *</label>
                                <textarea name="shipping_address" id="shipping_address" rows="3" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom dark:bg-gray-800 dark:text-white">{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Hidden fields for region IDs -->
                            <input type="hidden" name="province_id" id="province_id" value="{{ old('province_id') }}">
                            <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id') }}">
                            <input type="hidden" name="district_id" id="district_id" value="{{ old('district_id') }}">
                            <input type="hidden" name="village_id" id="village_id" value="{{ old('village_id') }}">
                            <input type="hidden" name="rajaongkir_city_id" id="rajaongkir_city_id" value="{{ old('rajaongkir_city_id') }}">

                            <div>
                                <label for="province_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Provinsi *</label>
                                <select name="province_select" id="province_select" required class="w-full">
                                    <option value="">Klik untuk memilih provinsi...</option>
                                </select>
                                <div id="province-loading" class="hidden mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-block animate-spin mr-2">⏳</span> Memuat provinsi...
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Klik dropdown untuk memuat daftar provinsi</p>
                                @error('province_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="city_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kota/Kabupaten *</label>
                                <select name="city_select" id="city_select" required class="w-full" disabled>
                                    <option value="">Pilih Provinsi terlebih dahulu</option>
                                </select>
                                <div id="city-loading" class="hidden mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-block animate-spin mr-2">⏳</span> Memuat kota...
                                </div>
                                @error('city_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="district_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kecamatan *</label>
                                <select name="district_select" id="district_select" required class="w-full" disabled>
                                    <option value="">Pilih Kota terlebih dahulu</option>
                                </select>
                                <div id="district-loading" class="hidden mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-block animate-spin mr-2">⏳</span> Memuat kecamatan...
                                </div>
                                @error('district_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="village_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kelurahan/Desa</label>
                                <select name="village_select" id="village_select" class="w-full" disabled>
                                    <option value="">Pilih Kecamatan terlebih dahulu</option>
                                </select>
                                <div id="village-loading" class="hidden mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-block animate-spin mr-2">⏳</span> Memuat kelurahan...
                                </div>
                            </div>

                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kode Pos</label>
                                <input type="text" name="shipping_postal_code" id="shipping_postal_code" value="{{ old('shipping_postal_code') }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom dark:bg-gray-800 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Options -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                        <h2 class="text-xl font-bold text-black dark:text-white mb-6">Shipping</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="shipping_courier" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kurir *</label>
                                <select name="shipping_courier" id="shipping_courier" required disabled class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom dark:bg-gray-800 dark:text-white">
                                    <option value="">Pilih Kota terlebih dahulu</option>
                                </select>
                                <div id="courier-loading" class="hidden mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-block animate-spin mr-2">⏳</span> Memuat daftar kurir...
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih kota terlebih dahulu untuk mengaktifkan pilihan kurir</p>
                                @error('shipping_courier')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="shipping-services" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Layanan Pengiriman</label>
                                <div id="shipping-services-list" class="space-y-2">
                                    <!-- Shipping services will be loaded here -->
                                </div>
                            </div>

                            <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                            <input type="hidden" name="shipping_service" id="shipping_service" value="">
                        </div>
                    </div>

                    <!-- Shipping Cost Summary -->
                    <div id="shipping-summary" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 hidden">
                        <h2 class="text-xl font-bold text-black dark:text-white mb-4">Ringkasan Biaya Pengiriman</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Kurir:</span>
                                <span id="summary-courier" class="font-medium text-black dark:text-white">-</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Layanan:</span>
                                <span id="summary-service" class="font-medium text-black dark:text-white">-</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Estimasi:</span>
                                <span id="summary-etd" class="font-medium text-black dark:text-white">-</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-black dark:text-white">Biaya Ongkir:</span>
                                    <span id="summary-shipping-cost" class="font-bold text-emerald-custom">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-emerald-custom text-white font-bold rounded-xl hover:bg-[#0ea572] transition-colors">
                            Continue to Payment
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-black dark:text-white mb-6">Order Summary</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center gap-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-black dark:text-white">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Qty: {{ $quantity }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-800 pt-4 space-y-2">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span class="font-semibold text-black dark:text-white" id="subtotal-display">Rp {{ number_format($product->price * $quantity, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span class="font-semibold text-black dark:text-white" id="shipping-display">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-black dark:text-white pt-4 border-t border-gray-200 dark:border-gray-800">
                            <span>Total</span>
                            <span class="text-emerald-custom" id="total-display">Rp {{ number_format($product->price * $quantity, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subtotal = {{ $product->price * $quantity }};
    let shippingCost = 0;

    // Initialize Tom Select for provinces
    // Load all provinces when dropdown is opened
    const provinceSelect = new TomSelect('#province_select', {
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        placeholder: 'Klik untuk memilih provinsi...',
        preload: 'focus',
        load: function(query, callback) {
            // Show loading
            document.getElementById('province-loading').classList.remove('hidden');
            
            // Load all provinces, filter server-side if query provided
            const url = '{{ route("api.provinces") }}' + (query ? '?search=' + encodeURIComponent(query) : '');
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('province-loading').classList.add('hidden');
                    console.log('Provinces API Response:', data);
                    if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
                        callback(data.data);
                    } else {
                        console.warn('No provinces data or empty array:', data);
                        callback([]);
                    }
                })
                .catch(error => {
                    console.error('Error loading provinces:', error);
                    document.getElementById('province-loading').classList.add('hidden');
                    callback([]);
                });
        },
        onChange: function(value) {
            document.getElementById('province_id').value = value;
            
            // Reset all child dropdowns when province changes
            citySelect.clearOptions();
            citySelect.clear();
            citySelect.disable();
            document.getElementById('city_id').value = '';
            document.getElementById('rajaongkir_city_id').value = '';
            
            districtSelect.clearOptions();
            districtSelect.clear();
            districtSelect.disable();
            document.getElementById('district_id').value = '';
            
            villageSelect.clearOptions();
            villageSelect.clear();
            villageSelect.disable();
            document.getElementById('village_id').value = '';
            
            courierSelect.disabled = true;
            courierSelect.value = '';
            
            if (value) {
                citySelect.enable();
                // Show loading indicator
                document.getElementById('city-loading').classList.remove('hidden');
                // Preload all cities for selected province first
                const loadUrl = '{{ route("api.cities", ":id") }}'.replace(':id', value);
                fetch(loadUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Hide loading indicator
                        document.getElementById('city-loading').classList.add('hidden');
                        console.log('Cities API Response:', data);
                        
                        if (data.error) {
                            console.error('API Error:', data.error);
                            citySelect.clearOptions();
                            return;
                        }
                        
                        if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
                            // Add all cities to Tom Select (Komerce.id API format: {id, name, zip_code})
                            const cities = data.data.map(item => ({
                                id: item.id,
                                name: item.name
                            }));
                            citySelect.addOptions(cities);
                            console.log(`Loaded ${cities.length} cities for province ${value}`);
                        } else {
                            console.warn('No cities found for province:', value, data);
                            citySelect.clearOptions();
                        }
                    })
                    .catch(error => {
                        // Hide loading indicator
                        document.getElementById('city-loading').classList.add('hidden');
                        console.error('Error loading cities:', error);
                        citySelect.clearOptions();
                    });
                
                // Load function for search
                citySelect.load(function(query, callback) {
                    const url = '{{ route("api.cities", ":id") }}'.replace(':id', value) + (query ? '?search=' + encodeURIComponent(query) : '');
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.data && Array.isArray(data.data)) {
                                // Komerce.id API format: {id, name, zip_code}
                                callback(data.data.map(item => ({
                                    id: item.id,
                                    name: item.name
                                })));
                            } else {
                                callback([]);
                            }
                        })
                        .catch(error => {
                            console.error('Error searching cities:', error);
                            callback([]);
                        });
                });
            }
        }
    });

    // Initialize Tom Select for cities
    const citySelect = new TomSelect('#city_select', {
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        placeholder: 'Cari Kota/Kabupaten...',
        disabled: true,
        onChange: function(value, text) {
            document.getElementById('city_id').value = value;
            
            // With Komerce.id API, city ID is already compatible with RajaOngkir
            if (value) {
                // City ID from Komerce.id API is already the same as RajaOngkir
                document.getElementById('rajaongkir_city_id').value = value;
                
                // Load couriers from RajaOngkir API
                loadCouriers();
                
                // Recalculate shipping if courier already selected
                const courier = courierSelect.value;
                if (courier) {
                    calculateShipping(courier, value);
                }
            } else {
                document.getElementById('rajaongkir_city_id').value = '';
                courierSelect.disable();
            }
            
            // Reset child dropdowns when city changes
            districtSelect.clearOptions();
            districtSelect.clear();
            districtSelect.disable();
            document.getElementById('district_id').value = '';
            
            villageSelect.clearOptions();
            villageSelect.clear();
            villageSelect.disable();
            document.getElementById('village_id').value = '';
            
            if (value) {
                districtSelect.enable();
                // Preload all districts for selected city first
                const loadUrl = '{{ route("api.districts", ":id") }}'.replace(':id', value);
                fetch(loadUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Hide loading indicator
                        document.getElementById('district-loading').classList.add('hidden');
                        if (data.error) {
                            console.error('API Error:', data.error);
                            districtSelect.clearOptions();
                            return;
                        }
                        if (data.data && data.data.length > 0) {
                            districtSelect.addOptions(data.data);
                        } else {
                            console.warn('No districts found for city:', value);
                            districtSelect.clearOptions();
                        }
                    })
                    .catch(error => {
                        // Hide loading indicator
                        document.getElementById('district-loading').classList.add('hidden');
                        console.error('Error loading districts:', error);
                        districtSelect.clearOptions();
                    });
                
                // Load function for search
                districtSelect.load(function(query, callback) {
                    const url = '{{ route("api.districts", ":id") }}'.replace(':id', value) + (query ? '?search=' + encodeURIComponent(query) : '');
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                callback([]);
                                return;
                            }
                            callback(data.data || []);
                        })
                        .catch(error => {
                            console.error('Error searching districts:', error);
                            callback([]);
                        });
                });
            }
        }
    });

    // Initialize Tom Select for districts
    const districtSelect = new TomSelect('#district_select', {
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        placeholder: 'Cari Kecamatan...',
        disabled: true,
        onChange: function(value) {
            document.getElementById('district_id').value = value;
            
            // Reset village dropdown when district changes
            villageSelect.clearOptions();
            villageSelect.clear();
            villageSelect.disable();
            document.getElementById('village_id').value = '';
            
            if (value) {
                villageSelect.enable();
                // Show loading indicator
                document.getElementById('village-loading').classList.remove('hidden');
                // Preload all villages for selected district first
                const loadUrl = '{{ route("api.villages", ":id") }}'.replace(':id', value);
                fetch(loadUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Hide loading indicator
                        document.getElementById('village-loading').classList.add('hidden');
                        if (data.error) {
                            console.error('API Error:', data.error);
                            villageSelect.clearOptions();
                            return;
                        }
                        if (data.data && data.data.length > 0) {
                            villageSelect.addOptions(data.data);
                        } else {
                            console.warn('No villages found for district:', value);
                            villageSelect.clearOptions();
                        }
                    })
                    .catch(error => {
                        // Hide loading indicator
                        document.getElementById('village-loading').classList.add('hidden');
                        console.error('Error loading villages:', error);
                        villageSelect.clearOptions();
                    });
                
                // Load function for search
                villageSelect.load(function(query, callback) {
                    const url = '{{ route("api.villages", ":id") }}'.replace(':id', value) + (query ? '?search=' + encodeURIComponent(query) : '');
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                callback([]);
                                return;
                            }
                            callback(data.data || []);
                        })
                        .catch(error => {
                            console.error('Error searching villages:', error);
                            callback([]);
                        });
                });
            }
        }
    });

    // Initialize Tom Select for villages
    const villageSelect = new TomSelect('#village_select', {
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        placeholder: 'Cari Kelurahan/Desa...',
        disabled: true,
        onChange: function(value) {
            document.getElementById('village_id').value = value;
        }
    });

    // Handle shipping cost calculation
    const courierSelect = document.getElementById('shipping_courier');
    const shippingServicesDiv = document.getElementById('shipping-services');
    const shippingServicesList = document.getElementById('shipping-services-list');
    
    // Load couriers from RajaOngkir API
    function loadCouriers() {
        const loadingDiv = document.getElementById('courier-loading');
        loadingDiv.classList.remove('hidden');
        courierSelect.disabled = true;
        courierSelect.innerHTML = '<option value="">Memuat kurir...</option>';
        
        fetch('{{ route("api.shipping.couriers") }}')
            .then(response => response.json())
            .then(data => {
                loadingDiv.classList.add('hidden');
                if (data.success && data.data && data.data.length > 0) {
                    courierSelect.innerHTML = '<option value="">Pilih Kurir</option>';
                    data.data.forEach(courier => {
                        const option = document.createElement('option');
                        option.value = courier.code;
                        option.textContent = courier.name;
                        courierSelect.appendChild(option);
                    });
                    courierSelect.disabled = false;
                } else {
                    courierSelect.innerHTML = '<option value="">Gagal memuat kurir</option>';
                    console.error('Failed to load couriers:', data.message);
                }
            })
            .catch(error => {
                loadingDiv.classList.add('hidden');
                courierSelect.innerHTML = '<option value="">Gagal memuat kurir</option>';
                console.error('Error loading couriers:', error);
            });
    }

    courierSelect.addEventListener('change', function() {
        const courier = this.value;
        const rajaongkirCityId = document.getElementById('rajaongkir_city_id').value;

        if (!courier || !rajaongkirCityId) {
            shippingServicesDiv.classList.add('hidden');
            updateShippingCost(0, '');
            if (!rajaongkirCityId) {
                shippingServicesList.innerHTML = '<div class="text-sm text-yellow-600">Pilih kota terlebih dahulu untuk menghitung ongkir.</div>';
            }
            return;
        }

        // Calculate shipping cost using RajaOngkir city ID
        calculateShipping(courier, rajaongkirCityId);
    });

    // Also recalculate when city changes (handled in onChange above)

    function calculateShipping(courier, destination) {
        const origin = {{ config('services.rajaongkir.origin_city_id', '444') }}; // City ID origin toko (Surabaya)
        const weight = document.getElementById('product_weight').value;
        
        // destination should be RajaOngkir city ID
        if (!destination) {
            shippingServicesDiv.classList.add('hidden');
            updateShippingCost(0, '');
            return;
        }

        shippingServicesList.innerHTML = '<div class="text-sm text-gray-500">Menghitung ongkir...</div>';

        fetch('{{ route("api.shipping.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                origin: origin,
                destination: destination,
                weight: weight,
                courier: courier
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                const courierData = data.data[0];
                shippingServicesList.innerHTML = '';

                if (!courierData.costs || courierData.costs.length === 0) {
                    shippingServicesList.innerHTML = '<div class="text-sm text-red-600">Tidak ada layanan pengiriman tersedia untuk kurir ini.</div>';
                    updateShippingCost(0, '');
                    return;
                }

                // Store all services for summary display
                let allServices = [];
                
                courierData.costs.forEach(cost => {
                    if (!cost.cost || cost.cost.length === 0) {
                        return;
                    }
                    
                    cost.cost.forEach(service => {
                        const serviceId = `${courier}_${cost.service}_${service.value}`;
                        const etd = service.etd ? service.etd.replace(' HARI', '').replace(' hari', '') : '-';
                        const etdText = etd !== '-' ? `${etd} hari` : 'Estimasi belum tersedia';
                        
                        // Store service info
                        allServices.push({
                            service: cost.service,
                            cost: service.value,
                            etd: etdText
                        });
                        
                        const label = document.createElement('label');
                        label.className = 'flex items-center p-3 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors';
                        label.innerHTML = `
                            <input type="radio" name="shipping_service_radio" value="${serviceId}" data-cost="${service.value}" data-service="${cost.service}" data-etd="${etdText}" class="mr-3 text-emerald-custom focus:ring-emerald-custom">
                            <div class="flex-1">
                                <div class="font-medium text-black dark:text-white">${cost.service}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">${etdText} - Rp ${parseInt(service.value).toLocaleString('id-ID')}</div>
                            </div>
                        `;
                        label.addEventListener('change', function() {
                            if (this.querySelector('input').checked) {
                                const selectedEtd = this.querySelector('input').getAttribute('data-etd');
                                updateShippingCost(service.value, cost.service, selectedEtd);
                            }
                        });
                        shippingServicesList.appendChild(label);
                    });
                });
                
                // Update summary with all available services
                updateShippingSummary(allServices);

                shippingServicesDiv.classList.remove('hidden');
            } else {
                const errorMsg = data.message || 'Gagal menghitung ongkir. Silakan coba lagi.';
                shippingServicesList.innerHTML = `<div class="text-sm text-red-600">${errorMsg}</div>`;
                updateShippingCost(0, '');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            shippingServicesList.innerHTML = '<div class="text-sm text-red-600">Terjadi kesalahan saat menghitung ongkir.</div>';
            updateShippingCost(0, '');
        });
    }

    function updateShippingCost(cost, service, etd = '') {
        shippingCost = parseInt(cost) || 0;
        document.getElementById('shipping_cost').value = shippingCost;
        document.getElementById('shipping_service').value = service || '';
        
        const total = subtotal + shippingCost;
        
        document.getElementById('shipping-display').textContent = 'Rp ' + shippingCost.toLocaleString('id-ID');
        document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
        
        // Update shipping summary
        const courierSelect = document.getElementById('shipping_courier');
        const courierName = courierSelect.options[courierSelect.selectedIndex]?.text || '-';
        document.getElementById('summary-courier').textContent = courierName;
        document.getElementById('summary-service').textContent = service || '-';
        document.getElementById('summary-etd').textContent = etd || '-';
        document.getElementById('summary-shipping-cost').textContent = 'Rp ' + shippingCost.toLocaleString('id-ID');
        
        // Show summary if shipping cost > 0
        const summaryDiv = document.getElementById('shipping-summary');
        if (shippingCost > 0) {
            summaryDiv.classList.remove('hidden');
        } else {
            summaryDiv.classList.add('hidden');
        }
    }
    
    function updateShippingSummary(allServices) {
        // This function can be used to display all available services if needed
        // Currently, services are displayed in the shipping services list
    }
});
</script>
@endpush
@endsection
