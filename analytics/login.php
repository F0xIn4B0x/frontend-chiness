<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 5 hardcoded sector users with passwords
    $users = [
    'law' => 'fujitsulaw',
    'agriculture' => 'fujitsuagriculture',
    'government' => 'fujitsugovernment',
    'insurance' => 'fujitsuinsurance',
    'realEstate' => 'fujitsurealEstate',
];

// Hardcoded API keys per user (passed through session)
$apiKeys = [
    'law' => 'APIKEY-LAW-12345',
    'agriculture' => 'APIKEY-AGRI-23456',
    'government' => 'APIKEY-GOV-34567',
    'insurance' => 'APIKEY-INS-45678',
    'realEstate' => 'APIKEY-RE-56789',
];

// Preluăm datele
$inputID = $_POST['company_id'] ?? "";
$password = $_POST['password'] ?? "";

// Switch-case based authentication
$authenticated = false;
$userName = null;
$userSector = null;

switch ($inputID) {
    case 'law':
        if ($password === $users['law']) {
            $authenticated = true;
            $userName = 'Law Sector';
            $userSector = 'law';
        }
        break;
    case 'agriculture':
        if ($password === $users['agriculture']) {
            $authenticated = true;
            $userName = 'Agriculture Sector';
            $userSector = 'agriculture';
        }
        break;
    case 'government':
        if ($password === $users['government']) {
            $authenticated = true;
            $userName = 'Government Sector';
            $userSector = 'government';
        }
        break;
    case 'insurance':
        if ($password === $users['insurance']) {
            $authenticated = true;
            $userName = 'Insurance Sector';
            $userSector = 'insurance';
        }
        break;
    case 'realEstate':
        if ($password === $users['realEstate']) {
            $authenticated = true;
            $userName = 'Real Estate Sector';
            $userSector = 'realEstate';
        }
        break;
    default:
        $authenticated = false;
        break;
}

    // Verify authentication and set session
    if ($authenticated) {
    // Set session data to pass to analytics.php
    $_SESSION['analytics_logged'] = true;
    $_SESSION['analytics_user'] = $inputID;
    $_SESSION['analytics_name'] = $userName;
    $_SESSION['analytics_sector'] = $userSector;
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
    // Set API key for this session (if available)
    $_SESSION['api_key'] = $apiKeys[$inputID] ?? '';
    
    // Redirect to analytics.php
    header('Location: analytics.php');
    exit;
} else {
    // EROARE - Mesaj formal
    $msg = "Eroare: Credențialele furnizate nu figurează în baza noastră de date.";
    // Redirecționăm înapoi cu mesaj de eroare
    header("Location: login.php?type=error&msg=" . urlencode($msg));
    exit;
}
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MD Analytics | Acces Securizat</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="login-container">
        
        <h2>MD Analytics</h2>

        <form id="loginForm" action="login.php" method="POST">
            
            <div class="form-group">
                <label for="company_id">Identificator Fiscal:</label>
                <input type="text" id="company_id" name="company_id" placeholder="CUI" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Parolă:</label>
                <input type="password" id="password" name="password" placeholder="Cheie de Acces" required>
            </div>

            <button type="submit">Autentificare</button>
        </form>

        <div class="footer-link">
            <a href="../aboutus.php">Ați întâmpinat dificultăți la autentificare?<br> Contactați-ne!</a>
        </div>

        <div id="mesaj-container" class="mesaj-box"></div>
    </div>

    

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const msg = urlParams.get('msg');
            const type = urlParams.get('type');
            
            if (msg) {
                const container = document.getElementById('mesaj-container');
                container.style.display = 'block';
                container.innerText = "NOTIFICARE: " + msg;
                
                if (type === 'success') {
                    container.classList.add('success');
                } else {
                    container.classList.add('error');
                }
                
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

</body>
</html>