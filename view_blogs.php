<?php
$host = 'localhost';
$db = 'blogdb';
$user = 'root';
$pass = ''; // Change this if needed

$conn = new mysqli($host, $user, $pass, $db);
$posts = [];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Blog Posts</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
      animation: fadeIn 0.5s ease-out;
    }
    .post {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .post:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen p-4 sm:p-8">
  <div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8 fade-in">All Blog Posts</h1>
    <div class="text-center mb-8">
      <a 
        href="blog.html" 
        class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:scale-105 fade-in"
      >
        Write a New Post
      </a>
    </div>

    <?php if (count($posts) === 0): ?>
      <p class="text-center text-gray-600 fade-in">No blog posts found.</p>
    <?php endif; ?>

    <?php foreach ($posts as $index => $post): ?>
      <div class="post bg-white rounded-xl shadow-md p-6 mb-6 fade-in" style="animation-delay: <?php echo $index * 0.1 ?>s;">
        <h2 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($post['title']); ?></h2>
        <time class="text-sm text-gray-500 block mb-3"><?php echo htmlspecialchars($post['created_at']); ?></time>
        <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
      </div>
    <?php endforeach; ?>

    <footer class="text-center mt-12 text-gray-500">
      <a href="index.html" class="hover:text-indigo-600 transition duration-200">Homepage</a>
    </footer>
  </div>
</body>
</html>