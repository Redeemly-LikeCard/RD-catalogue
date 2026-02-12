<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue Integration Test Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">
            Redeemly Catalogue Integration Test Dashboard
        </h1>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Test Endpoints</h2>

            <div class="space-y-4">
                <!-- External Sign In Test -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-700 mb-2">1. External Sign In Test</h3>
                    <p class="text-sm text-gray-600 mb-2">Tests external authentication with the catalogue API.</p>
                    <button onclick="testEndpoint('GET', '/catalogue-test/external-sign-in')"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        Test External Sign In
                    </button>
                    <div id="external-sign-in-result" class="mt-2"></div>
                </div>

                <!-- Get Catalogue Test -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-700 mb-2">2. Get Catalogue Test</h3>
                    <p class="text-sm text-gray-600 mb-2">Tests retrieving catalogue vouchers from the API.</p>
                    <button onclick="testEndpoint('GET', '/catalogue-test/catalogue')"
                            class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
                        Test Get Catalogue
                    </button>
                    <div id="catalogue-result" class="mt-2"></div>
                </div>

                <!-- Pull SKU Test -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-700 mb-2">3. Pull SKU Test</h3>
                    <p class="text-sm text-gray-600 mb-2">Tests pulling SKU codes for a specific voucher.</p>
                    <div class="mb-2">
                        <input type="text" id="voucherId" placeholder="Voucher ID"
                               class="border rounded px-2 py-1 mr-2" value="test-voucher-123">
                        <input type="number" id="quantity" placeholder="Quantity"
                               class="border rounded px-2 py-1 mr-2" value="5">
                        <input type="text" id="orderRef" placeholder="Order Ref (optional)"
                               class="border rounded px-2 py-1 mr-2">
                        <input type="text" id="customerRef" placeholder="Customer Ref (optional)"
                               class="border rounded px-2 py-1 mr-2" value="Net1234">
                    </div>
                    <button onclick="testPullSku()"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">
                        Test Pull SKU
                    </button>
                    <div id="pull-sku-result" class="mt-2"></div>
                </div>

                <!-- Customer Log Test -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-700 mb-2">4. Customer Log Test</h3>
                    <p class="text-sm text-gray-600 mb-2">Tests retrieving customer logs.</p>
                    <div class="mb-2">
                        <input type="number" id="page" placeholder="Page"
                               class="border rounded px-2 py-1 mr-2" value="1">
                        <input type="number" id="pageSize" placeholder="Page Size"
                               class="border rounded px-2 py-1 mr-2" value="10">
                        <input type="text" id="customerRef" placeholder="Customer Ref (optional)"
                               class="border rounded px-2 py-1 mr-2" value="Net1234">
                        <select id="type" class="border rounded px-2 py-1 mr-2">
                            <option value="1">New</option>
                            <option value="2" selected>Revealed</option>
                            <option value="3">Redeemed</option>
                            <option value="4">Expired</option>
                        </select>
                    </div>
                    <button onclick="testCustomerLog()"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        Test Customer Log
                    </button>
                    <div id="customer-log-result" class="mt-2"></div>
                </div>

                <!-- Get Token Test -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-700 mb-2">5. Get Token Test</h3>
                    <p class="text-sm text-gray-600 mb-2">Tests retrieving the current cached access token.</p>
                    <button onclick="testEndpoint('GET', '/catalogue-test/token')"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded">
                        Test Get Token
                    </button>
                    <div id="token-result" class="mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Configuration Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Configuration Status</h2>
            <div id="config-status" class="text-sm">
                <p class="mb-2"><strong>API Base URL:</strong> <span id="api-url">Loading...</span></p>
                <p class="mb-2"><strong>API Key:</strong> <span id="api-key">Loading...</span></p>
                <p class="mb-2"><strong>Client ID:</strong> <span id="client-id">Loading...</span></p>
            </div>
        </div>
    </div>

    <script>
        // Test endpoint function
        async function testEndpoint(method, url, data = null) {
            const resultDiv = url.replace('/catalogue-test/', '') + '-result';
            const resultElement = document.getElementById(resultDiv);

            resultElement.innerHTML = '<div class="text-blue-500">Testing...</div>';

            try {
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                };

                if (data && method !== 'GET') {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(url, options);
                const result = await response.json();

                resultElement.innerHTML = `
                    <div class="mt-2 p-3 rounded ${result.success ? 'bg-green-100 border-green-400' : 'bg-red-100 border-red-400'} border">
                        <div class="font-semibold ${result.success ? 'text-green-800' : 'text-red-800'}">
                            ${result.success ? '✅ Success' : '❌ Failed'}
                        </div>
                        <div class="text-sm mt-1">${result.message}</div>
                        ${result.error ? `<div class="text-xs text-red-600 mt-1">Error: ${result.error.message || result.error}</div>` : ''}
                        ${result.data ? `<pre class="text-xs mt-2 bg-gray-50 p-2 rounded overflow-x-auto">${JSON.stringify(result.data, null, 2)}</pre>` : ''}
                    </div>
                `;
            } catch (error) {
                resultElement.innerHTML = `
                    <div class="mt-2 p-3 rounded bg-red-100 border-red-400 border">
                        <div class="font-semibold text-red-800">❌ Error</div>
                        <div class="text-sm mt-1">${error.message}</div>
                    </div>
                `;
            }
        }

        // Test Pull SKU with form data
        function testPullSku() {
            const data = {
                voucherId: document.getElementById('voucherId').value,
                quantity: parseInt(document.getElementById('quantity').value),
                orderRef: document.getElementById('orderRef').value || null,
                customerRef: document.getElementById('customerRef').value || null
            };

            testEndpoint('POST', '/catalogue-test/pull-sku', data);
        }

        // Test Customer Log with form data
        function testCustomerLog() {
            const params = new URLSearchParams({
                Page: document.getElementById('page').value,
                PageSize: document.getElementById('pageSize').value,
                CustomerRef: document.getElementById('customerRef').value || 'Net1234',
                type: document.getElementById('type').value
            });

            testEndpoint('GET', '/catalogue-test/customer-log?' + params.toString());
        }

        // Load configuration status
        async function loadConfigStatus() {
            try {
                document.getElementById('api-url').textContent = '{{ config("catalogue.base_url") }}';
                document.getElementById('api-key').textContent = '{{ config("catalogue.credentials.api_key") ? "Configured" : "Not configured" }}';
                document.getElementById('client-id').textContent = '{{ config("catalogue.credentials.client_id") ? "Configured" : "Not configured" }}';
            } catch (error) {
                console.error('Failed to load config status:', error);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', loadConfigStatus);
    </script>
</body>
</html>
