<?php
// Load the required Composer autoload file
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables using Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['txtUserInput'], $_POST['page'])) {
    $query = $_POST['txtUserInput'];
    $page = (int)$_POST['page'];

    $url = "https://api.unsplash.com/search/photos?query=" . urlencode($query) .
        '&client_id=' . $_ENV["UNSPLASH_ACCESS_KEY"] . // API key from environment variables
        '&per_page=14&page=' . $page; // Set number of images per page and the current page number

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request and fetch the response
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    // Decode the JSON response from the API
    $data = json_decode($response, true);

    // Check if the API returned any errors
    if (isset($data['errors']) && !empty($data['errors'])) {
        echo "<p>Error: " . implode(", ", $data['errors']) . "</p>";
    } else {
        $images = $data['results'] ?? []; // Fetch the images from the response

        if (!empty($images)) {
            foreach ($images as $image) {
                $image_url = $image['urls']['regular'];
                $alt_description = $image['alt_description'] ?? 'Image';

                // Render the image in an HTML container
                echo "
                <div class='image-container'>
                    <img src='$image_url' alt='$alt_description'>
                </div>";
            }
        } else {
            echo "<p>No images found for \"$query\".</p>";
        }
    }
}
