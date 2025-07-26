<?php
// Enable error reporting and logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

// Database connection configuration
$servername = "localhost";
$username = "root"; // Default for local setups like XAMPP, replace with your username
$password = ""; // Default for local setups like XAMPP, replace with your password
$dbname = "portfolio_cookies";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $error_msg = "Connection failed: " . $conn->connect_error;
    error_log($error_msg);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $error_msg]);
    exit;
}

// Get user data
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$consent_status = isset($_POST['consent']) ? $_POST['consent'] : 'pending';

// Generate or retrieve cookie ID
$cookie_id = isset($_COOKIE['user_cookie_id']) ? $_COOKIE['user_cookie_id'] : bin2hex(random_bytes(16));
$cookie_expiry = time() + (365 * 24 * 60 * 60); // 1 year
setcookie('user_cookie_id', $cookie_id, $cookie_expiry, "/");

// Use default values for testing (comment out to re-enable API)
// $city = 'TestCity';
// $country = 'TestCountry';
$location_url = "http://ip-api.com/json/{$ip_address}";
$location_data = @json_decode(file_get_contents($location_url), true);
if ($location_data === false || (isset($location_data['status']) && $location_data['status'] == 'fail')) {
    error_log("Geolocation API failed for IP: $ip_address");
    $city = 'Unknown';
    $country = 'Unknown';
} else {
    $city = isset($location_data['city']) ? $location_data['city'] : 'Unknown';
    $country = isset($location_data['country']) ? $location_data['country'] : 'Unknown';
}

// Check if the cookie_id already exists
$sql_check = "SELECT id, page_visit_count FROM cookie_tracking WHERE cookie_id = ?";
$stmt_check = $conn->prepare($sql_check);
if ($stmt_check === false) {
    $error_msg = "Prepare failed (check): " . $conn->error;
    error_log($error_msg);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $error_msg]);
    exit;
}
$stmt_check->bind_param("s", $cookie_id);
if (!$stmt_check->execute()) {
    $error_msg = "Execute failed (check): " . $stmt_check->error;
    error_log($error_msg);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $error_msg]);
    exit;
}
$result = $stmt_check->get_result();

$success = false;
if ($result->num_rows > 0) {
    // Update existing record
    $row = $result->fetch_assoc();
    $new_visit_count = $row['page_visit_count'] + 1;
    $sql_update = "UPDATE cookie_tracking SET consent_status = ?, city = ?, country = ?, user_agent = ?, visit_timestamp = CURRENT_TIMESTAMP, page_visit_count = ? WHERE cookie_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update === false) {
        error_log("Prepare failed (update): " . $conn->error);
    } else {
        $stmt_update->bind_param("ssssis", $consent_status, $city, $country, $user_agent, $new_visit_count, $cookie_id);
        if ($stmt_update->execute()) {
            $success = true;
        } else {
            error_log("Update failed: " . $stmt_update->error);
        }
        $stmt_update->close();
    }
} else {
    // Insert new record
    $page_visit_count = 1;
    $sql_insert = "INSERT INTO cookie_tracking (ip_address, city, country, consent_status, user_agent, cookie_id, page_visit_count) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    if ($stmt_insert === false) {
        error_log("Prepare failed (insert): " . $conn->error);
    } else {
        $stmt_insert->bind_param("ssssssi", $ip_address, $city, $country, $consent_status, $user_agent, $cookie_id, $page_visit_count);
        if ($stmt_insert->execute()) {
            $success = true;
        } else {
            error_log("Insert failed: " . $stmt_insert->error);
        }
        $stmt_insert->close();
    }
}

$stmt_check->close();
$conn->close();

// Return response
header('Content-Type: application/json');
echo json_encode(['status' => $success ? 'success' : 'partial', 'cookie_id' => $cookie_id, 'ip_address' => $ip_address, 'consent' => $consent_status, 'city' => $city, 'country' => $country]);
?>