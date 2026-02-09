@extends('layouts.catalog')

@section('title', 'Checkout')

@section('content')
<!-- Loading overlay saat submit checkout -->
<div id="checkout-loading-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-sm w-full mx-4 text-center shadow-2xl">
        <div class="inline-block w-12 h-12 border-4 border-emerald-custom border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-lg font-semibold text-black dark:text-white">Memproses checkout...</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Jangan tutup halaman ini</p>
    </div>
</div>

<div class="min-h-screen bg-white dark:bg-black py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-black dark:text-white mb-8">Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('payment.process') }}" method="POST" class="space-y-6" id="checkout-form">
                    @csrf
                    @if($errors->any())
                        <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-4">
                            <p class="font-semibold text-red-700 dark:text-red-300">Perbaiki hal berikut:</p>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-600 dark:text-red-400">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
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

                            <input type="hidden" name="province_id" id="province_id" value="{{ old('province_id') }}">
                            <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id') }}">
                            <input type="hidden" name="district_id" id="district_id" value="{{ old('district_id') }}">
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

                            <div id="shipping-detail-box" class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800/50 hidden">
                                <h3 class="text-sm font-semibold text-black dark:text-white mb-2">Detail Pengiriman</h3>
                                <div class="text-sm space-y-1">
                                    <p><span class="text-gray-500 dark:text-gray-400">Dari:</span> <span id="origin-label">{{ config('services.rajaongkir.origin_label', 'Toko') }}</span></p>
                                    <p><span class="text-gray-500 dark:text-gray-400">Ke:</span> <span id="dest-label" class="text-black dark:text-white">Pilih provinsi, kota, dan kecamatan</span></p>
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
                                    <option value="">Pilih Kecamatan terlebih dahulu</option>
                                </select>
                                <div id="courier-loading" class="hidden mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-block animate-spin mr-2">⏳</span> Memuat daftar kurir...
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih kecamatan terlebih dahulu untuk mengaktifkan pilihan kurir</p>
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

                            <input type="hidden" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', '0') }}">
                            <input type="hidden" name="shipping_service" id="shipping_service" value="{{ old('shipping_service', '') }}">
                            @error('shipping_service')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('shipping_courier')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                        <button type="submit" id="checkout-submit-btn" class="px-8 py-4 bg-emerald-custom text-white font-bold rounded-xl hover:bg-[#0ea572] transition-colors cursor-pointer">
                            Continue to Payment
                        </button>
                    </div>
                    <p id="checkout-submit-hint" class="mt-2 text-sm text-amber-600 dark:text-amber-400 hidden">Pilih layanan pengiriman (klik salah satu opsi ongkir) agar biaya ongkir tercatat.</p>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-black dark:text-white mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-4">
                        <div class="flex gap-3">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                            @else
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex-shrink-0"></div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-black dark:text-white text-sm">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $quantity }} × Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-sm font-medium text-black dark:text-white mt-1" id="order-summary-item-total">Rp {{ number_format($product->price * $quantity, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-800 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Subtotal (item)</span>
                            <span class="font-medium text-black dark:text-white" id="subtotal-display">Rp {{ number_format($product->price * $quantity, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400" id="order-summary-shipping-row">
                            <span>Ongkir</span>
                            <span class="font-medium text-black dark:text-white" id="shipping-display">Rp 0</span>
                        </div>
                        <div id="order-summary-courier-detail" class="text-xs text-gray-500 dark:text-gray-400 pl-0">
                            <span id="order-summary-courier-text">Pilih kurir & layanan</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-black dark:text-white pt-3 border-t border-gray-200 dark:border-gray-800">
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
// Wait for jQuery to be available
(function() {
    function initCheckout() {
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded!');
            return;
        }

    const subtotal = {{ $product->price * $quantity }};
    let shippingCost = 0;

    // Sebelum submit: sync ongkir + tampilkan loading overlay (cegah double submit)
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        var radio = document.querySelector('input[name="shipping_service_radio"]:checked');
        if (radio) {
            document.getElementById('shipping_cost').value = radio.getAttribute('data-cost') || '0';
            document.getElementById('shipping_service').value = radio.getAttribute('data-service') || '';
        }
        var overlay = document.getElementById('checkout-loading-overlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            this.querySelector('button[type="submit"]').disabled = true;
        }
    });

    // Provinsi diambil dari: route('api.provinces') => /api/provinces (backend memanggil rajaongkir.komerce.id/api/v1/destination/province)
    const provinceSelect = new TomSelect('#province_select', {
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        placeholder: 'Klik untuk memilih provinsi...',
        preload: 'focus',
        load: function(query, callback) {
            // Show loading
            $('#province-loading').removeClass('hidden');
            
            // Load all provinces, filter server-side if query provided
            const url = '{{ route("api.provinces") }}' + (query ? '?search=' + encodeURIComponent(query) : '');
            
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#province-loading').addClass('hidden');
                    // Backend selalu return { data: [...] }; terima juga array langsung
                    var list = (data && Array.isArray(data.data)) ? data.data : (Array.isArray(data) ? data : []);
                    if (list.length > 0) {
                        callback(list);
                    } else {
                        callback([]);
                        // Pesan untuk user: pastikan RAJAONGKIR_API_KEY di .env
                        var msg = (data && data.error) ? data.error : 'Data provinsi kosong. Pastikan RAJAONGKIR_API_KEY di file .env sudah diisi.';
                        provinceSelect.sync();
                        if (!$('#province-api-error').length) {
                            $('#province-loading').after('<p id="province-api-error" class="mt-2 text-sm text-amber-600 dark:text-amber-400">' + msg + '</p>');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('#province-loading').addClass('hidden');
                    callback([]);
                    var errMsg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Gagal memuat provinsi. Cek RAJAONGKIR_API_KEY di .env.';
                    if (!$('#province-api-error').length) {
                        $('#province-loading').after('<p id="province-api-error" class="mt-2 text-sm text-amber-600 dark:text-amber-400">' + errMsg + '</p>');
                    }
                }
            });
        },
        onChange: function(value) {
            $('#province_id').val(value);
            
            // Reset all child dropdowns when province changes
            citySelect.clearOptions();
            citySelect.clear();
            citySelect.disable();
            $('#city_id').val('');
            $('#rajaongkir_city_id').val('');
            
            districtSelect.clearOptions();
            districtSelect.clear();
            districtSelect.disable();
            $('#district_id').val('');
            courierSelect.disabled = true;
            courierSelect.value = '';
            $('#shipping-detail-box').addClass('hidden');
            if (value) {
                citySelect.enable();
                // Show loading indicator
                $('#city-loading').removeClass('hidden');
                // Preload all cities for selected province first
                const loadUrl = '{{ route("api.cities", ":id") }}'.replace(':id', value);
                
                $.ajax({
                    url: loadUrl,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Hide loading indicator
                        $('#city-loading').addClass('hidden');
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
                    },
                    error: function(xhr, status, error) {
                        // Hide loading indicator
                        $('#city-loading').addClass('hidden');
                        console.error('Error loading cities:', error);
                        console.error('XHR Response:', xhr.responseText);
                        citySelect.clearOptions();
                    }
                });
                
                // Load function for search
                citySelect.load(function(query, callback) {
                    const url = '{{ route("api.cities", ":id") }}'.replace(':id', value) + (query ? '?search=' + encodeURIComponent(query) : '');
                    
                    $.ajax({
                        url: url,
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data && data.data && Array.isArray(data.data)) {
                                // Komerce.id API format: {id, name, zip_code}
                                callback(data.data.map(item => ({
                                    id: item.id,
                                    name: item.name
                                })));
                            } else {
                                callback([]);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error searching cities:', error);
                            callback([]);
                        }
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
            $('#city_id').val(value);
            if (value) {
                $('#rajaongkir_city_id').val(value);
            } else {
                $('#rajaongkir_city_id').val('');
            }
            districtSelect.clearOptions();
            districtSelect.clear();
            districtSelect.disable();
            $('#district_id').val('');
            courierSelect.disabled = true;
            courierSelect.value = '';
            $('#shipping-detail-box').addClass('hidden');
            if (value) {
                districtSelect.enable();
                // Preload all districts for selected city first
                const loadUrl = '{{ route("api.districts", ":id") }}'.replace(':id', value);
                $('#district-loading').removeClass('hidden');
                
                $.ajax({
                    url: loadUrl,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Hide loading indicator
                        $('#district-loading').addClass('hidden');
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
                    },
                    error: function(xhr, status, error) {
                        // Hide loading indicator
                        $('#district-loading').addClass('hidden');
                        console.error('Error loading districts:', error);
                        console.error('XHR Response:', xhr.responseText);
                        districtSelect.clearOptions();
                    }
                });
                
                // Load function for search
                districtSelect.load(function(query, callback) {
                    const url = '{{ route("api.districts", ":id") }}'.replace(':id', value) + (query ? '?search=' + encodeURIComponent(query) : '');
                    
                    $.ajax({
                        url: url,
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.error) {
                                callback([]);
                                return;
                            }
                            callback(data.data || []);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error searching districts:', error);
                            callback([]);
                        }
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
            if (value) {
                loadCouriers();
                courierSelect.disabled = false;
                var pOpt = provinceSelect.getOption(provinceSelect.value);
                var cOpt = citySelect.getOption(citySelect.value);
                var dOpt = districtSelect.getOption(value);
                var pText = (pOpt && pOpt.name) ? pOpt.name : '';
                var cText = (cOpt && cOpt.name) ? cOpt.name : '';
                var dText = (dOpt && dOpt.name) ? dOpt.name : '';
                var destStr = [pText, cText, dText].filter(Boolean).join(', ') || 'Pilih provinsi, kota, dan kecamatan';
                $('#dest-label').text(destStr);
                $('#shipping-detail-box').removeClass('hidden');
                var courier = courierSelect.value;
                if (courier) {
                    calculateShipping(courier, value);
                }
            } else {
                courierSelect.disabled = true;
                courierSelect.value = '';
                $('#shipping-detail-box').addClass('hidden');
            }
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
        const districtId = document.getElementById('district_id').value;
        if (!courier || !districtId) {
            shippingServicesDiv.classList.add('hidden');
            updateShippingCost(0, '');
            if (!districtId) {
                shippingServicesList.innerHTML = '<div class="text-sm text-yellow-600">Pilih kecamatan terlebih dahulu untuk menghitung ongkir.</div>';
            }
            return;
        }
        calculateShipping(courier, districtId);
    });

    // Also recalculate when city changes (handled in onChange above)

    function calculateShipping(courier, destinationDistrictId) {
        const originDistrictId = {{ config('services.rajaongkir.origin_district_id', '1391') }};
        const weight = document.getElementById('product_weight').value || 1000;
        if (!destinationDistrictId) {
            shippingServicesDiv.classList.add('hidden');
            updateShippingCost(0, '');
            return;
        }
        shippingServicesList.innerHTML = '<div class="text-sm text-gray-500">Memanggil API ongkir...</div>';
        shippingServicesDiv.classList.remove('hidden');
        fetch('{{ route("api.shipping.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                origin: originDistrictId,
                destination: parseInt(destinationDistrictId, 10),
                weight: parseInt(weight, 10) || 1000,
                courier: courier
            })
        })
        .then(function(response) {
            return response.json().then(function(data) {
                if (!response.ok) {
                    throw new Error(data.message || 'HTTP ' + response.status);
                }
                return data;
            });
        })
        .then(function(data) {
            var list = data.data;
            if (!list) list = [];
            if (!Array.isArray(list)) list = [list];
            if (data.success && list.length > 0) {
                var courierData = list[0];
                var costs = courierData.costs || courierData.services || [];
                shippingServicesList.innerHTML = '';

                if (costs.length === 0) {
                    shippingServicesList.innerHTML = '<div class="text-sm text-red-600">Tidak ada layanan untuk kurir ini.</div>';
                    updateShippingCost(0, '');
                    return;
                }

                costs.forEach(function(cost) {
                    var serviceName = cost.service || cost.name || 'Layanan';
                    var costArr = cost.cost || (cost.price !== undefined ? [{ value: cost.price, etd: cost.etd || '-' }] : []);
                    if (!costArr.length) return;
                    costArr.forEach(function(s) {
                        var val = typeof s === 'object' ? (s.value ?? s.price ?? 0) : s;
                        var etd = typeof s === 'object' ? (s.etd || '-') : '-';
                        var etdText = (etd !== '-' && etd !== '') ? (String(etd).replace(' HARI', '').replace(' hari', '') + ' hari') : 'Estimasi belum tersedia';
                        var serviceId = courier + '_' + serviceName + '_' + val;
                        var radio = document.createElement('input');
                        radio.type = 'radio';
                        radio.name = 'shipping_service_radio';
                        radio.value = serviceId;
                        radio.setAttribute('data-cost', val);
                        radio.setAttribute('data-service', serviceName);
                        radio.setAttribute('data-etd', etdText);
                        radio.className = 'mr-3 text-emerald-custom focus:ring-emerald-custom';
                        var label = document.createElement('label');
                        label.className = 'flex items-center p-3 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors';
                        label.appendChild(radio);
                        var div = document.createElement('div');
                        div.className = 'flex-1';
                        div.innerHTML = '<div class="font-medium text-black dark:text-white">' + serviceName + '</div><div class="text-sm text-gray-600 dark:text-gray-400">' + etdText + ' - Rp ' + parseInt(val, 10).toLocaleString('id-ID') + '</div>';
                        label.appendChild(div);
                        function syncShipping() {
                            if (radio.checked) updateShippingCost(radio.getAttribute('data-cost'), radio.getAttribute('data-service'), radio.getAttribute('data-etd'));
                        }
                        radio.addEventListener('change', syncShipping);
                        label.addEventListener('click', function() { setTimeout(syncShipping, 0); });
                        shippingServicesList.appendChild(label);
                    });
                });

                shippingServicesDiv.classList.remove('hidden');
            } else {
                var errorMsg = data.message || 'Gagal hit API ongkir. Cek RAJAONGKIR_CALCULATE_API_KEY di .env.';
                shippingServicesList.innerHTML = '<div class="text-sm text-red-600">' + errorMsg + '</div>';
                updateShippingCost(0, '');
            }
        })
        .catch(function(err) {
            console.error('Ongkir API error:', err);
            shippingServicesList.innerHTML = '<div class="text-sm text-red-600">Gagal memanggil API ongkir: ' + (err.message || 'Silakan coba lagi.') + '</div>';
            updateShippingCost(0, '');
        });
    }

    function updateShippingCost(cost, service, etd) {
        cost = parseInt(cost, 10) || 0;
        service = service || '';
        etd = etd || '-';
        shippingCost = cost;
        var shippingCostEl = document.getElementById('shipping_cost');
        var shippingServiceEl = document.getElementById('shipping_service');
        if (shippingCostEl) shippingCostEl.value = cost;
        if (shippingServiceEl) shippingServiceEl.value = service;
        var total = subtotal + cost;
        var shippingDisplay = document.getElementById('shipping-display');
        var totalDisplay = document.getElementById('total-display');
        if (shippingDisplay) shippingDisplay.textContent = 'Rp ' + cost.toLocaleString('id-ID');
        if (totalDisplay) totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        var courierSelectEl = document.getElementById('shipping_courier');
        var courierName = (courierSelectEl && courierSelectEl.selectedIndex >= 0 && courierSelectEl.options[courierSelectEl.selectedIndex]) ? courierSelectEl.options[courierSelectEl.selectedIndex].text : '-';
        var summaryCourier = document.getElementById('summary-courier');
        var summaryService = document.getElementById('summary-service');
        var summaryEtd = document.getElementById('summary-etd');
        var summaryShippingCost = document.getElementById('summary-shipping-cost');
        if (summaryCourier) summaryCourier.textContent = courierName;
        if (summaryService) summaryService.textContent = service || '-';
        if (summaryEtd) summaryEtd.textContent = etd;
        if (summaryShippingCost) summaryShippingCost.textContent = 'Rp ' + cost.toLocaleString('id-ID');
        var summaryDiv = document.getElementById('shipping-summary');
        var courierDetailEl = document.getElementById('order-summary-courier-text');
        if (summaryDiv) {
            if (cost > 0 && service) summaryDiv.classList.remove('hidden'); else summaryDiv.classList.add('hidden');
        }
        if (courierDetailEl) courierDetailEl.textContent = (cost > 0 && service) ? (courierName + ' - ' + service) : 'Pilih kurir & layanan';
    }
    
    function updateShippingSummary(allServices) {}
    } // End initCheckout function
    
    // Try to initialize when jQuery is ready
    if (typeof $ !== 'undefined') {
        $(document).ready(initCheckout);
    } else {
        // Wait for jQuery to load
        window.addEventListener('load', function() {
            if (typeof $ !== 'undefined') {
                $(document).ready(initCheckout);
            } else {
                console.error('jQuery failed to load!');
            }
        });
    }
})();
</script>
@endpush
@endsection
