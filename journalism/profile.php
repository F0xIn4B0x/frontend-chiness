<?php
session_start();
// Require login session for journalists
if (empty($_SESSION['journalist_logged'])) {
    header('Location: login.php');
    exit;
}

// Get journalist profile info from session
$profile = $_SESSION['journalist_profile'] ?? [
    'username' => 'Unknown',
    'name' => 'Journalist',
];

// Count articles in /articles (exclude non-.php files)
$articlesDir = __DIR__ . '/../articles';
$articleCount = 0;
if (is_dir($articlesDir)) {
    $files = scandir($articlesDir);
    foreach ($files as $file) {
        if (substr($file, -4) === '.php' && $file !== '.' && $file !== '..') {
            $articleCount++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journalist Profile - Infrastructure Gap</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef1f5; padding: 30px 0; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 40px; }
        h1 { color: #2c3e50; }
        .profile-info { margin-bottom: 30px; }
        .profile-info label { font-weight: bold; color: #333; }
        .profile-info span { margin-left: 10px; color: #555; }
        .article-count { font-size: 1.2em; color: #e74c3c; font-weight: bold; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #e74c3c; color: #fff; border: none; border-radius: 6px; text-decoration: none; font-weight: bold; transition: background 0.2s; }
        .btn:hover { background: #c0392b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👤 Journalist Profile</h1>
        <div class="profile-info">
            <p><label>Name:</label> <span><?php echo htmlspecialchars($profile['name']); ?></span></p>
            <p><label>Username:</label> <span><?php echo htmlspecialchars($profile['username']); ?></span></p>
        </div>
        <div>
            <p>You have published <span class="article-count"><?php echo $articleCount; ?></span> article<?php echo $articleCount === 1 ? '' : 's'; ?>.</p>
        </div>
        <a href="postcreate.php" class="btn">Create New Article</a>
        <a href="../index.php" class="btn" style="background:#ccc;color:#333;margin-left:10px;">Home</a>
    </div>
</body>
</html>
