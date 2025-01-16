<?php
// Load environment variables from .env file (located in the root directory)
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['txtUserInput'])) {
    //User Input
    $query = $_POST['txtUserInput'];

    //API Url
    $url = "https://api.unsplash.com/search/photos?query=" . urlencode($query) . '&client_id=' . $_ENV["UNSPLASH_ACCESS_KEY"] . '&per_page=30';

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Execute
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    if (isset($data['errors']) && !empty($data['errors'])) {
        $imageHTML = "<p>Error: " . implode(", ", $data['errors']) . "</p>";
    } else {
        $images = isset($data['results']) ? $data['results'] : [];

        $imageHTML = '';
        if (!empty($images)) {
            foreach ($images as $image) {
                $image_url = $image['urls']['regular'];
                $imageHTML .= "<img src=\"$image_url\" alt=\"$query\">";
            }
        } else {
            $imageHTML = "<p>No images found for \"$query\".</p>";
        }
    }

    echo $imageHTML;
}