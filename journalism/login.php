<?php
// Simple login page for journalists
session_start();

// default message variables
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hardcoded credentials per request
    if ($username === 'recorder' && $password === 'fujitsu') {
        // successful — set session flag, store profile, and redirect to profile page
        $_SESSION['journalist_logged'] = true;
        $_SESSION['journalist_user'] = $username;
        // Store profile info (expand as needed)
        $_SESSION['journalist_profile'] = [
            'username' => $username,
            'name' => 'Recorder Staff', // You can customize or fetch real name here
        ];
        header('Location: profile.php');
        exit;
    } else {
        $errorMsg = 'Invalid credentials. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnaliști - Logare</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .login-container { max-width:420px;margin:40px auto;padding:20px;background:#fff;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,.06); }
        .login-container h2{margin-bottom:12px}
        .form-group{margin-bottom:12px}
        input[type=text],input[type=password]{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px}
        button{background:#e74c3c;color:#fff;border:none;padding:10px 14px;border-radius:6px;cursor:pointer}
        .mesaj-box{margin-top:12px;padding:10px;border-radius:6px}
        .mesaj-box.error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Jurnaliști — Logare</h2>

        <?php if ($errorMsg): ?>
            <div id="mesaj-container" class="mesaj-box error"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <form id="loginForm" action="login.php" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Utilizator" required autocomplete="off">
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Parola" required>
            </div>

            <button type="submit">Logare</button>
        </form>
        

        <div class="footer-link" style="margin-top:10px;">
            <a href="login-companie.php">Reprezentați o persoană juridică? Accesați portalul dedicat aici.</a>
        </div>
    </div>

</body>
</html>