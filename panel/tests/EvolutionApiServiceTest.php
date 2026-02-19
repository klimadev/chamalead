<?php

declare(strict_types=1);

/**
 * Evolution API Service Test Suite
 *
 * Comprehensive tests for the EvolutionApiService class with mocked
 * cURL calls and JSON fixtures. No external dependencies required.
 *
 * @package Panel\Tests
 * @author Chamalead
 * @version 1.0.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Mock cURL functions for testing
 *
 * These functions override the global cURL functions to provide
 * predictable responses without making actual HTTP calls.
 */
$mockCurlResponses = [];
$mockCurlOptions = [];

function curl_init($url = null): mixed
{
    global $mockCurlOptions;
    $mockCurlOptions = [];
    return curl_setopt(null, CURLOPT_URL, $url);
}

function curl_setopt($ch, $option, $value): bool
{
    global $mockCurlOptions;
    $mockCurlOptions[$option] = $value;
    return true;
}

function curl_exec($ch): bool|string
{
    global $mockCurlResponses;

    $url = $mockCurlOptions[CURLOPT_URL] ?? '';
    $method = $mockCurlOptions[CURLOPT_CUSTOMREQUEST] ?? 'GET';

    $key = $method . ' ' . $url;

    if (isset($mockCurlResponses[$key])) {
        return $mockCurlResponses[$key];
    }

    return json_encode(['error' => 'No mock response configured']);
}

function curl_getinfo($ch, $option): mixed
{
    global $mockCurlResponses;
    $url = $mockCurlOptions[CURLOPT_URL] ?? '';
    $method = $mockCurlOptions[CURLOPT_CUSTOMREQUEST] ?? 'GET';
    $key = $method . ' ' . $url;

    if ($option === CURLINFO_HTTP_CODE) {
        return $mockCurlResponses[$key . '_code'] ?? 200;
    }

    return null;
}

function curl_error($ch): string
{
    global $mockCurlResponses;
    $url = $mockCurlOptions[CURLOPT_URL] ?? '';
    $method = $mockCurlOptions[CURLOPT_CUSTOMREQUEST] ?? 'GET';
    $key = $method . ' ' . $url;

    return $mockCurlResponses[$key . '_error'] ?? '';
}

function curl_close($ch): void
{
    // No-op for testing
}

/**
 * Test class for EvolutionApiService
 */
class EvolutionApiServiceTest extends TestCase
{
    private EvolutionApiService $api;
    private string $testCachePath;

    protected function setUp(): void
    {
        // Set up test environment
        $this->testCachePath = sys_get_temp_dir() . '/panel_test_cache_' . uniqid();
        putenv('EVOLUTION_API_URL=http://test-api.example.com');
        putenv('EVOLUTION_API_KEY=test-key-12345');
        putenv('CACHE_ENABLED=true');
        putenv('CACHE_PATH=' . $this->testCachePath);
        putenv('CACHE_TTL=300');
        putenv('API_TIMEOUT=10');

        // Clear mock responses
        global $mockCurlResponses;
        $mockCurlResponses = [];

        // Create test cache directory
        if (!is_dir($this->testCachePath)) {
            mkdir($this->testCachePath, 0750, true);
        }

        // Initialize service
        $this->api = new EvolutionApiService();
    }

    protected function tearDown(): void
    {
        // Clean up test cache
        if (is_dir($this->testCachePath)) {
            $files = glob($this->testCachePath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->testCachePath);
        }

        // Reset environment
        putenv('EVOLUTION_API_URL');
        putenv('EVOLUTION_API_KEY');
        putenv('CACHE_ENABLED');
        putenv('CACHE_PATH');
        putenv('CACHE_TTL');
        putenv('API_TIMEOUT');
    }

    /**
     * Configure mock cURL response
     */
    private function mockResponse(
        string $method,
        string $endpoint,
        array $data,
        int $httpCode = 200,
        string $error = ''
    ): void {
        global $mockCurlResponses;
        $url = 'http://test-api.example.com' . $endpoint;
        $key = $method . ' ' . $url;
        $mockCurlResponses[$key] = json_encode($data);
        $mockCurlResponses[$key . '_code'] = $httpCode;
        $mockCurlResponses[$key . '_error'] = $error;
    }

    /**
     * Test that fetchInstances returns an array
     */
    public function testFetchInstancesReturnsArray(): void
    {
        $mockData = [
            [
                'name' => 'test-instance-1',
                'instanceName' => 'test-instance-1',
                'connectionStatus' => 'open',
                'ownerJid' => '1234567890@s.whatsapp.net',
            ],
            [
                'name' => 'test-instance-2',
                'instanceName' => 'test-instance-2',
                'connectionStatus' => 'closed',
                'ownerJid' => '0987654321@s.whatsapp.net',
            ],
        ];

        $this->mockResponse('GET', '/instance/fetchInstances', $mockData, 200);

        $result = $this->api->fetchInstances();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('test-instance-1', $result[0]['name']);
        $this->assertEquals('open', $result[0]['connectionStatus']);
    }

    /**
     * Test that fetchInstances uses cache when available
     */
    public function testFetchInstancesUsesCache(): void
    {
        $mockData = [
            [
                'name' => 'cached-instance',
                'connectionStatus' => 'open',
            ],
        ];

        // First call - should hit API
        $this->mockResponse('GET', '/instance/fetchInstances', $mockData, 200);
        $result1 = $this->api->fetchInstances();

        // Second call - should use cache (no new mock needed)
        $result2 = $this->api->fetchInstances();

        $this->assertEquals($result1, $result2);
        $this->assertEquals('cached-instance', $result2[0]['name']);
    }

    /**
     * Test that creating an instance invalidates the cache
     */
    public function testCacheInvalidationOnCreate(): void
    {
        // Initial fetch
        $initialData = [['name' => 'old-instance']];
        $this->mockResponse('GET', '/instance/fetchInstances', $initialData, 200);
        $this->api->fetchInstances();

        // Create new instance
        $createResponse = [
            'success' => true,
            'instance' => ['name' => 'new-instance'],
        ];
        $this->mockResponse('POST', '/instance/create', $createResponse, 201);
        $this->api->createInstance(['instanceName' => 'new-instance']);

        // Next fetch should hit API again (cache invalidated)
        $newData = [
            ['name' => 'old-instance'],
            ['name' => 'new-instance'],
        ];
        $this->mockResponse('GET', '/instance/fetchInstances', $newData, 200);
        $result = $this->api->fetchInstances();

        $this->assertCount(2, $result);
    }

    /**
     * Test cache signature validation (HMAC)
     */
    public function testCacheSignatureValidation(): void
    {
        // Create a cache file with invalid signature
        $cacheKey = 'instances_all';
        $cacheFile = $this->testCachePath . '/' . md5($cacheKey) . '.cache';

        $tamperedData = [
            'expires' => time() + 3600,
            'value' => [['name' => 'tampered-data']],
        ];

        file_put_contents($cacheFile, serialize($tamperedData));

        // API should return fresh data (cache should be validated)
        $freshData = [['name' => 'fresh-data']];
        $this->mockResponse('GET', '/instance/fetchInstances', $freshData, 200);

        $result = $this->api->fetchInstances();

        // Should get fresh data, not tampered data
        $this->assertEquals('fresh-data', $result[0]['name']);
    }

    /**
     * Test successful instance creation
     */
    public function testCreateInstanceSuccess(): void
    {
        $mockResponse = [
            'success' => true,
            'instance' => [
                'name' => 'created-instance',
                'instanceName' => 'created-instance',
            ],
        ];

        $this->mockResponse('POST', '/instance/create', $mockResponse, 201);

        $result = $this->api->createInstance([
            'instanceName' => 'created-instance',
            'token' => 'test-token',
        ]);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('created-instance', $result['data']['instance']['name']);
    }

    /**
     * Test instance creation failure
     */
    public function testCreateInstanceFailure(): void
    {
        $mockResponse = [
            'response' => [
                'message' => ['Instance name already exists'],
            ],
        ];

        $this->mockResponse('POST', '/instance/create', $mockResponse, 400);

        $result = $this->api->createInstance([
            'instanceName' => 'existing-instance',
        ]);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals(400, $result['status']);
        $this->assertStringContainsString('already exists', $result['message']);
    }

    /**
     * Test updating instance settings
     */
    public function testUpdateSettings(): void
    {
        $mockResponse = [
            'success' => true,
            'setting' => [
                'rejectCall' => true,
                'msgCall' => 'Busy',
            ],
        ];

        $this->mockResponse(
            'POST',
            '/settings/set/' . urlencode('test-instance'),
            $mockResponse,
            200
        );

        $result = $this->api->updateSettings('test-instance', [
            'rejectCall' => true,
            'msgCall' => 'Busy',
        ]);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('Busy', $result['data']['setting']['msgCall']);
    }

    /**
     * Test deleting an instance
     */
    public function testDeleteInstance(): void
    {
        $this->mockResponse(
            'DELETE',
            '/instance/delete/' . urlencode('instance-to-delete'),
            ['success' => true],
            200
        );

        $result = $this->api->deleteInstance('instance-to-delete');

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
    }

    /**
     * Test API health check
     */
    public function testHealthCheck(): void
    {
        $this->mockResponse('GET', '/instance/fetchInstances', [], 200);

        $isHealthy = $this->api->healthCheck();

        $this->assertTrue($isHealthy);
    }

    /**
     * Test health check with failed response
     */
    public function testHealthCheckFailure(): void
    {
        $this->mockResponse(
            'GET',
            '/instance/fetchInstances',
            ['error' => 'Service unavailable'],
            503
        );

        $isHealthy = $this->api->healthCheck();

        $this->assertFalse($isHealthy);
    }

    /**
     * Test handling of network timeout
     */
    public function testNetworkTimeout(): void
    {
        global $mockCurlResponses;
        $url = 'http://test-api.example.com/instance/fetchInstances';
        $key = 'GET ' . $url;
        $mockCurlResponses[$key] = false;
        $mockCurlResponses[$key . '_code'] = 0;
        $mockCurlResponses[$key . '_error'] = 'Connection timed out after 10001 milliseconds';

        $result = $this->api->fetchInstances();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test handling of malformed JSON response
     */
    public function testMalformedJsonResponse(): void
    {
        global $mockCurlResponses;
        $url = 'http://test-api.example.com/instance/fetchInstances';
        $key = 'GET ' . $url;
        $mockCurlResponses[$key] = '{invalid json}';  // Note: 'json' was misspelled as 'jon' in the original thought
        $mockCurlResponses[$key . '_code'] = 200;

        $result = $this->api->fetchInstances();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test handling of 500 Internal Server Error
     */
    public function testInternalServerError(): void
    {
        $this->mockResponse(
            'GET',
            '/instance/fetchInstances',
            ['error' => 'Internal Server Error'],
            500
        );

        $result = $this->api->fetchInstances();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test force refresh bypasses cache
     */
    public function testForceRefreshBypassesCache(): void
    {
        // Initial fetch
        $initialData = [['name' => 'initial-instance']];
        $this->mockResponse('GET', '/instance/fetchInstances', $initialData, 200);
        $this->api->fetchInstances();

        // Force refresh should hit API again
        $freshData = [['name' => 'fresh-instance']];
        $this->mockResponse('GET', '/instance/fetchInstances', $freshData, 200);
        $result = $this->api->fetchInstances(true);

        $this->assertEquals('fresh-instance', $result[0]['name']);
    }

    /**
     * Test getting settings for a specific instance
     */
    public function testGetSettings(): void
    {
        $mockSettings = [
            'rejectCall' => true,
            'msgCall' => 'Not available',
            'groupsIgnore' => false,
            'alwaysOnline' => true,
        ];

        $this->mockResponse(
            'GET',
            '/settings/find/' . urlencode('test-instance'),
            $mockSettings,
            200
        );

        $result = $this->api->getSettings('test-instance');

        $this->assertIsArray($result);
        $this->assertTrue($result['rejectCall']);
        $this->assertEquals('Not available', $result['msgCall']);
    }

    /**
     * Test getting single instance details
     */
    public function testGetInstance(): void
    {
        $mockData = [
            [
                'name' => 'instance-1',
                'connectionStatus' => 'open',
            ],
            [
                'instanceName' => 'instance-2',
                'connectionStatus' => 'closed',
            ],
        ];

        $this->mockResponse('GET', '/instance/fetchInstances', $mockData, 200);

        $result = $this->api->getInstance('instance-2');

        $this->assertIsArray($result);
        $this->assertEquals('instance-2', $result['instanceName']);
        $this->assertEquals('closed', $result['connectionStatus']);
    }

    /**
     * Test getting non-existent instance returns null
     */
    public function testGetNonExistentInstance(): void
    {
        $mockData = [['name' => 'existing-instance']];
        $this->mockResponse('GET', '/instance/fetchInstances', $mockData, 200);

        $result = $this->api->getInstance('non-existent');

        $this->assertNull($result);
    }

    /**
     * Test cache expiration
     */
    public function testCacheExpiration(): void
    {
        // Create expired cache entry
        $cacheKey = 'instances_all';
        $cacheFile = $this->testCachePath . '/' . md5($cacheKey) . '.cache';

        $expiredData = [
            'expires' => time() - 1, // Expired 1 second ago
            'value' => [['name' => 'expired-data']],
        ];

        file_put_contents($cacheFile, serialize($expiredData));

        // Should fetch fresh data
        $freshData = [['name' => 'fresh-data']];
        $this->mockResponse('GET', '/instance/fetchInstances', $freshData, 200);

        $result = $this->api->fetchInstances();

        $this->assertEquals('fresh-data', $result[0]['name']);
    }

    /**
     * Test clear cache functionality
     */
    public function testClearCache(): void
    {
        // Populate cache
        $mockData = [['name' => 'test-instance']];
        $this->mockResponse('GET', '/instance/fetchInstances', $mockData, 200);
        $this->api->fetchInstances();

        // Clear cache
        $this->api->clearCache();

        // Should fetch again from API
        $newData = [['name' => 'new-instance']];
        $this->mockResponse('GET', '/instance/fetchInstances', $newData, 200);
        $result = $this->api->fetchInstances();

        $this->assertEquals('new-instance', $result[0]['name']);
    }
}
