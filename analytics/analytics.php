<?php
// Start session and enforce authentication before any output
session_start();

// Redirect to login if not authenticated
if (empty($_SESSION['analytics_logged'])) {
    header('Location: login.php');
    exit;
}

// Prepare user session variables for display
$userName = $_SESSION['analytics_name'] ?? 'Unknown';
$userSector = $_SESSION['analytics_sector'] ?? 'unknown';
$loginTime = $_SESSION['login_time'] ?? 'N/A';
$username = $_SESSION['analytics_user'] ?? 'N/A';
// API key passed through session
$apiKey = $_SESSION['api_key'] ?? '';

// Server-side fetch of risks to allow PHP handling of GET and initial render
$risksEndpoint = 'http://localhost:8080/risks';
$risksItems = [];
$risksRaw = null;
$risksHttpCode = null;
$risksError = null;
try {
    $ch = curl_init($risksEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $risksRaw = curl_exec($ch);
    $curlErr = curl_error($ch);
    $risksHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($curlErr) {
        throw new Exception('cURL error: ' . $curlErr);
    }
    if ($risksHttpCode < 200 || $risksHttpCode >= 300) {
        throw new Exception('HTTP ' . $risksHttpCode . ' from ' . $risksEndpoint);
    }
    if ($risksRaw) {
        $parsed = json_decode($risksRaw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $risksItems = isset($parsed['content']) ? $parsed['content'] : (is_array($parsed) ? $parsed : []);
        }
    }
} catch (Exception $e) {
    $risksError = $e->getMessage();
}
// Fetch locations so we can resolve satRel ids to location objects
$locationsEndpoint = 'http://localhost:8080/locations';
$locationsItems = [];
$locationsError = null;
try {
    $ch2 = curl_init($locationsEndpoint);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 5);
    $locationsRaw = curl_exec($ch2);
    $curlErr2 = curl_error($ch2);
    $locationsHttpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);
    if ($curlErr2) {
        throw new Exception('cURL error: ' . $curlErr2);
    }
    if ($locationsHttpCode < 200 || $locationsHttpCode >= 300) {
        throw new Exception('HTTP ' . $locationsHttpCode . ' from ' . $locationsEndpoint);
    }
    if (!empty($locationsRaw)) {
        $parsedLoc = json_decode($locationsRaw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $locationsItems = isset($parsedLoc['content']) ? $parsedLoc['content'] : (is_array($parsedLoc) ? $parsedLoc : []);
        }
    }
} catch (Exception $e) {
    $locationsError = $e->getMessage();
}

// Build a map of locations by id for quick lookup
$locationsById = [];
foreach ($locationsItems as $loc) {
    if (isset($loc['id'])) {
        $locationsById[$loc['id']] = $loc;
    }
}
// Read filter values from GET and prepare PHP-side filtered array
$selectedType = $_GET['typerisk'] ?? '';
$selectedSatRel = isset($_GET['satrel']) ? $_GET['satrel'] : '';
$filteredRisks = [];
if (empty($risksError)) {
    foreach ($risksItems as $r) {
        $matchType = ($selectedType === '') || (isset($r['typerisk']) && $r['typerisk'] == $selectedType);
        $matchSat = ($selectedSatRel === '') || (isset($r['satRel']) && (string)$r['satRel'] === (string)$selectedSatRel);
        if ($matchType && $matchSat) {
            // Resolve satRel id to location object if available
            if (isset($r['satRel']) && isset($locationsById[$r['satRel']])) {
                $r['satRel'] = $locationsById[$r['satRel']];
            }
            $filteredRisks[] = $r;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Debug Dashboard</title>
    
    <style>
        /* === GLOBAL PAGE STYLE — DARK GOLD THEME === */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #0f0f0f;
    color: #f3e5ab;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* === HEADER === */
.header {
    background-color: #1a1a1a;
    color: #f3e5ab;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #c5a059;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
}

.header h1 {
    margin: 0;
    font-size: 1.5em;
    color: #f3e5ab;
}

.header-right {
    display: flex;
    gap: 20px;
    align-items: center;
}

.user-info {
    font-size: 0.95em;
    color: #f3e5ab;
}

.user-info strong {
    color: #c5a059;
}

/* Header Buttons */
.header .btn-home {
    color: #c5a059;
    text-decoration: none;
    padding: 5px 10px;
    border: 1px solid #c5a059;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.header .btn-home:hover {
    background-color: #c5a059;
    color: #000;
}

.btn-logout {
    color: #ff6b6b;
    text-decoration: none;
    padding: 5px 10px;
    border: 1px solid #ff6b6b;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.btn-logout:hover {
    background-color: #ff6b6b;
    color: #000;
}

/* === GRID & PANELS === */
.main-content-grid {
    display: grid;
    grid-template-columns: 1fr;
    grid-auto-rows: minmax(min-content, max-content);
    flex-grow: 1;
    gap: 10px;
    padding: 10px;
    box-sizing: border-box;
}

.panel {
    background-color: #1a1a1a;
    color: #f3e5ab;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
    padding: 20px;
    max-height: 90vh;
    overflow-y: auto;
    border-top: 3px solid #c5a059;
}

h2 {
    color: #c5a059;
    border-bottom: 1px solid #333;
    padding-bottom: 10px;
    margin-top: 0;
}

/* --- Form & Selector --- */
#risk-selector {
    padding: 8px;
    background-color: #252525;
    border: 1px solid #333;
    border-radius: 4px;
    width: 100%;
    margin-top: 5px;
    margin-bottom: 15px;
    color: #f3e5ab;
}

#risk-selector:focus {
    border-color: #c5a059;
    background-color: #2a2a2a;
    box-shadow: 0 0 8px rgba(197, 160, 89, 0.2);
    outline: none;
}

hr {
    border: 0;
    border-top: 1px solid #333;
    margin: 15px 0;
}

/* === PDF LIST PANEL === */
#pdf-list-container a {
    display: block;
    padding: 5px 0;
    color: #f3e5ab;
    text-decoration: none;
    border-bottom: 1px dotted #333;
}

#pdf-list-container a:hover {
    color: #c5a059;
    background-color: #252525;
}

.placeholder-text {
    color: #777;
    font-style: italic;
}

/* === RESPONSE PANEL (Dark Console Style) === */
.response-panel {
    background-color: #161616;
    color: #f3e5ab;
}

.response-header {
    margin-bottom: 10px;
    padding: 5px;
    border-bottom: 1px solid #333;
    font-weight: bold;
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

#response-status {
    color: #2ecc71;
    margin-right: 15px;
    font-size: 1.1em;
}

#http-status-code {
    background-color: #c5a059;
    color: #000;
    padding: 2px 5px;
    border-radius: 3px;
}

#response-json {
    white-space: pre-wrap;
    font-family: Consolas, monospace;
    font-size: 0.9em;
    padding: 10px;
    background-color: #1f1f1f;
    border-radius: 4px;
    border: 1px solid #333;
}

/* === TEMPLATE PANEL === */
.template-panel ul {
    list-style: square;
    padding-left: 20px;
}

.template-panel li {
    color: #f3e5ab;
}

/* === BUTTONS (GLOBAL) === */
button {
    padding: 12px;
    background-color: #c5a059;
    color: #000;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 700;
    text-transform: uppercase;
    transition: background-color 0.3s, transform 0.2s;
}

button:hover {
    background-color: #b08d55;
    transform: translateY(-2px);
}

    </style>
</head>
<body>
    
    <div class="header">
        <h1>🐞 Debug Console: Risk Data</h1>
        <div class="header-right">
            <div class="user-info">
                <strong><?php echo htmlspecialchars($userName); ?></strong> (<?php echo htmlspecialchars($userSector); ?>) 
                <br><small>Logged in: <?php echo htmlspecialchars($loginTime); ?></small>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
            <a href="/" class="btn-home">← Back to Dashboard</a>
        </div>
    </div>

    <div class="main-content-grid">
        <div class="panel filter-panel">
            <h2>🔎 Filter Risks</h2>
            <form method="get" id="filters-form">
            <div style="margin-bottom: 15px;">
                <label for="typerisk-filter"><strong>Filter by Type Risk:</strong></label>
                <select id="typerisk-filter" name="typerisk" onchange="document.getElementById('filters-form').submit();">
                    <option value="">-- All Types --</option>
                    <?php
                    $uniqueTypeRisks = [];
                    foreach ($risksItems as $risk) {
                        $typeRisk = $risk['typerisk'] ?? '';
                        if ($typeRisk && !in_array($typeRisk, $uniqueTypeRisks)) {
                            $uniqueTypeRisks[] = $typeRisk;
                        }
                    }
                    sort($uniqueTypeRisks);
                    foreach ($uniqueTypeRisks as $typeRisk): ?>
                        <option value="<?php echo htmlspecialchars($typeRisk); ?>" <?php echo ($selectedType === $typeRisk) ? 'selected' : ''; ?>><?php echo htmlspecialchars($typeRisk); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="satrel-filter"><strong>Filter by Location:</strong></label>
                <select id="satrel-filter" name="satrel" onchange="document.getElementById('filters-form').submit();">
                    <option value="">-- All Locations --</option>
                    <?php
                    $uniqueSatRels = [];
                    foreach ($risksItems as $risk) {
                        $satRel = $risk['satRel'] ?? null;
                        if ($satRel !== null && !in_array($satRel, $uniqueSatRels)) {
                            $uniqueSatRels[] = $satRel;
                        }
                    }
                    sort($uniqueSatRels);
                    foreach ($uniqueSatRels as $satRel):
                        $label = (isset($locationsById[$satRel]) ? (($locationsById[$satRel]['locatie'] ?? '') . ' (' . ($locationsById[$satRel]['judeti'] ?? '') . ')' ) : $satRel);
                        ?>
                        <option value="<?php echo htmlspecialchars($satRel); ?>" <?php echo ((string)$selectedSatRel === (string)$satRel) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button id="reset-filters" onclick="window.location='analytics.php'" style="width: 100%; padding: 8px; background-color: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1em;">Reset Filters</button>
            </form>
        </div>

        <div class="panel risks-container-panel">
            <h2>📋 Risk Items</h2>
            <div id="risks-container">
                <?php if (!empty($risksError)): ?>
                    <p class="placeholder-text">Error loading risks: <?php echo htmlspecialchars($risksError); ?></p>
                <?php else: ?>
                    <?php if (empty($filteredRisks)): ?>
                        <p class="placeholder-text">No risks match the selected filters.</p>
                    <?php else: ?>
                        <?php foreach ($filteredRisks as $risk): ?>
                            <div class="risk-item" data-id="<?php echo htmlspecialchars($risk['id'] ?? ''); ?>" data-typerisk="<?php echo htmlspecialchars($risk['typerisk'] ?? ''); ?>" data-satrel="<?php echo htmlspecialchars(is_array($risk['satRel']) ? ($risk['satRel']['id'] ?? '') : ($risk['satRel'] ?? '')); ?>">
                                <div class="risk-header">
                                    <strong>ID: <?php echo htmlspecialchars($risk['id'] ?? 'N/A'); ?></strong> | 
                                    <strong>Type: <?php echo htmlspecialchars($risk['typerisk'] ?? 'N/A'); ?></strong>
                                </div>
                                <div class="risk-info">
                                    <p><strong>Info:</strong> <?php echo htmlspecialchars($risk['info'] ?? 'N/A'); ?></p>
                                                    <p><strong>Location:</strong> <?php
                                                        $locDisplay = 'N/A';
                                                        if (isset($risk['satRel'])) {
                                                            if (is_array($risk['satRel'])) {
                                                                $loc = $risk['satRel'];
                                                                $locName = $loc['locatie'] ?? '';
                                                                $locJud = $loc['judeti'] ?? '';
                                                                $locDisplay = trim($locName . ($locJud ? ' (' . $locJud . ')' : '')) ?: 'N/A';
                                                            } else {
                                                                $locId = $risk['satRel'];
                                                                if (isset($locationsById[$locId])) {
                                                                    $l = $locationsById[$locId];
                                                                    $locDisplay = ($l['locatie'] ?? '') . ' (' . ($l['judeti'] ?? '') . ')';
                                                                } else {
                                                                    $locDisplay = htmlspecialchars((string)$locId);
                                                                }
                                                            }
                                                        }
                                                        echo htmlspecialchars($locDisplay);
                                                    ?></p>
                                    <p><strong>Files:</strong> <?php echo !empty($risk['arrayStringNumeFisiere']) ? htmlspecialchars(implode(', ', (array)$risk['arrayStringNumeFisiere'])) : 'None'; ?></p>
                                </div>
                                <div class="risk-timestamps">
                                    <small>Created: <?php echo htmlspecialchars($risk['createdAt'] ?? 'N/A'); ?></small><br>
                                    <small>Updated: <?php echo htmlspecialchars($risk['updatedAt'] ?? 'N/A'); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel response-panel">
            <h2>📊 API Response Debug</h2>
            <div class="response-header">
                <span id="response-status"><?php echo empty($risksError) ? 'OK' : 'Error'; ?></span>
                <span id="http-status-code"><?php echo htmlspecialchars($risksHttpCode ?? ''); ?></span>
                <span style="margin-left:15px; font-size:0.95em; color:#ecf0f1;">API Key: <code style="background:#ecf0f1;color:#2c3e50;padding:2px 6px;border-radius:3px;"><?php echo htmlspecialchars($apiKey); ?></code></span>
            </div>
            <pre id="response-json"><?php echo htmlspecialchars(!empty($risksRaw) ? json_encode($filteredRisks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ($risksError ?? 'No data')); ?></pre>
        </div>
    </div>

    <script>
        const typeRiskFilter = document.getElementById('typerisk-filter');
        const satRelFilter = document.getElementById('satrel-filter');
        const resetButton = document.getElementById('reset-filters');
        const riskItems = document.querySelectorAll('.risk-item');

        function applyFilters() {
            const selectedTypeRisk = typeRiskFilter.value;
            const selectedSatRel = satRelFilter.value;

            riskItems.forEach(item => {
                const itemTypeRisk = item.dataset.typerisk;
                const itemSatRel = item.dataset.satrel;

                const matchesTypeRisk = !selectedTypeRisk || itemTypeRisk === selectedTypeRisk;
                const matchesSatRel = !selectedSatRel || itemSatRel === selectedSatRel;

                if (matchesTypeRisk && matchesSatRel) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        typeRiskFilter.addEventListener('change', applyFilters);
        satRelFilter.addEventListener('change', applyFilters);

        resetButton.addEventListener('click', function() {
            typeRiskFilter.value = '';
            satRelFilter.value = '';
            applyFilters();
        });
    </script>

</body>
</html>