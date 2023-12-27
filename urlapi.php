<?php

// accept string of comma-separated values;
// explode string on commas; use each as ID
// use ID in API call; get MEI
// return ID and MEI

$sanitized_urls = filter_var($_GET['ids'], FILTER_SANITIZE_STRING);

$ids = [];
foreach (explode("\n", $sanitized_urls) as $url) {
    // $url = trim($url);
    if (preg_match('/\/asset-management\/([A-Z\d]*)(?![A-Z\d])/', $url, $matches)) {
        $ids[] = $matches[1];
    }
}

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
        'co_mei' => $id,
        'rep_mei' => $media_id,
        // 'api_url' => $api_url,
        // 'iiif_url' => $iiif_url
    ];
}

$iiif_parts = [
    "https://collections.newberry.org/IIIF3/Image/",
    "/full/max/0/default.jpg"
];


echo "<div class='csv-data' >";
echo "Compound_Object_MEI,Representative_Image_MEI,Representative_Image_IIIF_URL<br />";
foreach ($results as $row) {
    echo implode(',', $row) . "," , $iiif_parts[0] . htmlspecialchars($row['rep_mei']) . $iiif_parts[1] . '<br />';
}
echo "</div>";
echo "<div class='button-wrapper'>";
echo "<button _=\"on click writeText(my previousElementSibling's innerText) on navigator.clipboard put 'Copied!' into me wait 1s put 'Copy as CSV' into me\" class='csv-button' >Copy as CSV</button>";
echo "</div>";
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>Compound Object</th>";
echo "<th>Representative Image</th>";
echo "<th>Representative Image IIIF URL</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

foreach ($results as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['co_mei']) . "</td>";
    echo "<td>" . htmlspecialchars($row['rep_mei']) . "</td>";
    echo "<td><a href='" . $iiif_parts[0] . htmlspecialchars($row['rep_mei']) . $iiif_parts[1] . "' target='_blank'>";
    echo $iiif_parts[0] . htmlspecialchars($row['rep_mei']) . $iiif_parts[1];
    echo "</a></td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
