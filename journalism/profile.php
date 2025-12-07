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
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <title>Profil</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            background: #ffddc3;
            padding: 30px 0; 
            display: flex;
            justify-content: center;   /* horizontal center */
            align-items: center;       /* vertical center */
            min-height: 100vh;         /* full screen height */
            }
        .container { 
            text-align: center;
            max-width: 600px; 
            width: 600px;
            margin: 0 auto;
            background: #fff; 
            border-radius: 10px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            
        }
        h1 { margin-left: 20px;color: #111; }
        .profile-info { margin-left: 10px; margin-bottom: 30px; }
        .profile-info label { margin-left: 10px; font-weight: bold; color: #111; }
        .profile-info span { margin-left: 5px; color: #111; }
        .article-count { font-size: 1.2em; color: #DD2200; font-weight: bold; }
        .btn { 
            display: inline-block;
            margin-top: 20px; 
            margin-left: 20px;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #DD2200; 
            color: #fff; 
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s; }
        .btn:hover { background: #ff2600ff; }
        img {
            max-width: 150px;
            width: 150px;
            height: auto;
            border-radius: 8px;
            margin: 20px auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Journalist Profile</h1>
        <img src="../channels4_profile.jpg" alt="" />
        <div class="profile-info">
            <p><label>Name:</label> <span><?php echo htmlspecialchars($profile['name']); ?></span></p>
            <p><label>Username:</label> <span><?php echo htmlspecialchars($profile['username']); ?></span></p>
        </div>
        <div>
            <p>You have published <span class="article-count"><?php echo $articleCount; ?></span> article<?php echo $articleCount === 1 ? '' : 's'; ?>.</p>
        </div>
        <a href="postcreate.php" class="btn">Create New Article</a>
        <a href="logout.php" class="btn btn-logout" style="margin-left:10px;">Logout</a>
    </div>
</body>
</html>
