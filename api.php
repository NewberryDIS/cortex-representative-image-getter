<?php

// accept string of comma-separated values;
// explode string on commas; use each as ID
// use ID in API call; get MEI
// return ID and MEI

$sanitized_urls = filter_var($_GET['urls'], FILTER_SANITIZE_STRING);

$ids = [];
foreach (explode(",", $sanitized_urls) as $url) {
    if (preg_match('/\/asset-management\/([A-Z\d]*)(?![A-Z\d])/', $url, $matches)) {
        $ids[] = $matches[1];
    }
}
// echo json_encode($ids);
$results = [];

foreach ($ids as $id) {
    // Make API call
    $api_url = "https://collections.newberry.org/API/PackageExtractor/v1.0/Extract?Package=" . $id . "&RepresentativeFields=MediaEncryptedIdentifier&format=json";
    $response = file_get_contents($api_url);  // Or use cURL for more control

    // Check for errors
    if ($response === false) {
        $results[] = [
            'id' => $id,
            'error' => 'Failed to retrieve data',
        ];
        continue;
    }

    // Decode JSON response
    $data = json_decode($response, true);

    // Extract the MediaEncryptedIdentifier
    $media_id = $data['APIResponse']['Representative']['MediaEncryptedIdentifier'];

    $iiif_url = 'https://collections.newberry.org/IIIF3/Image/' . $media_id . '/full/max/0/default.jpg';
    // Add to results array
    $results[] = [
        'Compound_Object_MEI' => $id,
        'Representative_Image_MEI' => $media_id,
        // 'api_url' => $api_url,
        'Representative_Image_IIIF_URL' => $iiif_url
    ];
}

echo json_encode($results);
