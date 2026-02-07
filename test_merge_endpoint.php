<?php

/**
 * Test script to verify merge endpoint is working
 * Run: php test_merge_endpoint.php
 */

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test with a valid project ID
$projectId = 11; // Using first project from earlier

// Create a fake request
$request = Illuminate\Http\Request::create(
    "/dubsync/projects/{$projectId}/merge-full-transcript-audio",
    'POST',
    [],
    [],
    [],
    ['HTTP_ACCEPT' => 'application/json']
);

$response = $kernel->handle($request);

echo "Status: " . $response->status() . "\n";
echo "Content-Type: " . $response->headers->get('content-type') . "\n";
echo "Body:\n";
echo $response->getContent() . "\n";
