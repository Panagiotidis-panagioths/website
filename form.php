<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) {
    renderPage("Sign Up Status", "Connection failed: " . htmlspecialchars($conn->connect_error), "error");
    exit;
}

// Reusable HTML renderer
function renderPage($title, $message, $type = "info", $buttonText = "Back", $buttonLink = "form.html") {
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$title</title>
    <style>
        body { font-family: Arial; margin: 0; background: #fff; color: #333; }
        header { background: #e6e6fa; padding: 1rem; text-align: center; }
        .status-container {
            max-width: 600px; margin: 2rem auto; padding: 1.5rem;
            background: #f8f8ff; border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #4b0082; margin-bottom: 1rem; }
        p { text-align: center; font-size: 1.1rem; }
        .error { color: #d32f2f; }
        .success { color: #2e7d32; }
        .back-btn {
            display: block; width: 200px; margin: 1.5rem auto; padding: 0.75rem;
            background: linear-gradient(90deg, #ff3333, #ff6666); color: white;
            text-align: center; text-decoration: none; border-radius: 6px;
            font-weight: bold;
        }
        .back-btn:hover { background: linear-gradient(90deg, #e60000, #ff3333); }
        footer {
            background: #e6e6fa; text-align: center;
            font-size: 0.9rem; padding: 1rem; margin-top: 2rem; color: #4b0082;
        }
    </style>
</head>
<body>
    <header><h1>$title</h1></header>
    <div class='status-container'>
        <h2>$title</h2>
        <p class='$type'>$message</p>
        <a href='$buttonLink' class='back-btn'>$buttonText</a>
    </div>
    <footer>© Panagiotidis Panagiotis Chrysovalantis 2025</footer>
</body>
</html>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'signup') {
        if (!isset($_POST['name'], $_POST['lastname'], $_POST['phone'], $_POST['email'], $_POST['password'])) {
            renderPage("Sign Up Failed", "All fields are required for sign-up", "error");
            exit;
        }

        $name = $conn->real_escape_string($_POST['name']);
        $lastname = $conn->real_escape_string($_POST['lastname']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check_email = $conn->query("SELECT mail FROM users_data WHERE mail = '$email'");
        if ($check_email->num_rows > 0) {
            renderPage("Sign Up Failed", "Email already exists", "error");
            exit;
        }

        $insert = $conn->query("INSERT INTO users_data (name, lastname, phone, mail, password)
                                VALUES ('$name', '$lastname', '$phone', '$email', '$password')");

        if ($insert) {
            renderPage("Sign Up Successful", "Account created successfully!", "success", "Back", "form.html?mode=signin");
        } else {
            renderPage("Sign Up Failed", "Error: " . htmlspecialchars($conn->error), "error");
        }

    } elseif ($action === 'signin') {
        if (!isset($_POST['email'], $_POST['password'])) {
            renderPage("Sign In Failed", "Email and password are required", "error");
            exit;
        }

        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        $result = $conn->query("SELECT name, lastname, phone, mail, password FROM users_data WHERE mail = '$email'");

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $details = "<p><strong>Name:</strong> " . htmlspecialchars($user['name']) . "</p>
                            <p><strong>Last Name:</strong> " . htmlspecialchars($user['lastname']) . "</p>
                            <p><strong>Phone:</strong> " . htmlspecialchars($user['phone']) . "</p>
                            <p><strong>Email:</strong> " . htmlspecialchars($user['mail']) . "</p>";
                renderPage("Welcome, " . htmlspecialchars($user['name']) . "!", $details, "success");
            } else {
                renderPage("Sign In Failed", "Invalid email or password", "error");
            }
        } else {
            renderPage("Sign In Failed", "Invalid email or password", "error");
        }
    } else {
        renderPage("Error", "Invalid action", "error");
    }
} else {
    renderPage("Error", "Invalid request method", "error");
}

$conn->close();
?>
