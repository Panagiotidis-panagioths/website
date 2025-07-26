<?php
$host = 'localhost';
$db = 'blogdb';
$user = 'root';
$pass = ''; // Change this if needed

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if (!empty($title) && !empty($content)) {
    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();
    header("Location: view_blogs.php"); // Redirect to view posts
    exit;
} else {
    echo "Both title and content are required.";
}

$conn->close();
?>
