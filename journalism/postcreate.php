<?php
session_start();
// Require login session for journalists
if (empty($_SESSION['journalist_logged'])) {
    header('Location: login.php');
    exit;
}
require __DIR__ . '/../includes/upload.php';
$uploadResult = null;
$formResult = null;
$apiEndpoint = 'http://localhost:8080/risks'; // endpoint to create a risk record
$locationsEndpoint = 'http://localhost:8080/locations'; // endpoint to fetch locations
$risksValEndpoint = 'http://localhost:8080/risks-val'; // endpoint to fetch risk category options

// Function to fetch options from API
function fetchOptionsFromApi($endpoint) {
    if (empty($endpoint)) {
        throw new Exception('No API endpoint provided');
    }
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check for connection errors
    if ($error) {
        throw new Exception("API Connection Error: " . $error . " (Endpoint: " . $endpoint . ")");
    }

    // Check for HTTP errors
    if ($httpCode !== 200) {
        throw new Exception("API Server Error: HTTP " . $httpCode . " (Endpoint: " . $endpoint . "). Server may not be running.");
    }

    if (!$response) {
        throw new Exception("API Server Error: No response received from " . $endpoint . ". Server may not be running.");
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("API Response Error: Invalid JSON from " . $endpoint . " - " . json_last_error_msg());
    }

    // Handle standard API response format: { "status": true, "content": {...} or [...] }
    if (is_array($data) && isset($data['status']) && isset($data['content'])) {
        if ($data['status'] === true) {
            $content = $data['content'];

            // If content is an indexed array of scalar values (like risks)
            if (is_array($content) && isset($content[0]) && !is_array($content[0])) {
                $options = [];
                foreach ($content as $value) {
                    $options[] = ['id' => $value, 'name' => $value];
                }
                return $options;
            }

            // If content is an indexed array of associative arrays (like locations)
            if (is_array($content) && isset($content[0]) && is_array($content[0])) {
                $options = [];
                foreach ($content as $item) {
                    // Support the locations format: id, judeti, locatie
                    if (isset($item['id'])) {
                        $id = $item['id'];
                        $judet = $item['judeti'] ?? ($item['judet'] ?? '');
                        $loc = $item['locatie'] ?? ($item['localitate'] ?? '');
                        $name = $loc !== '' ? trim($loc . ' (' . $judet . ')') : $judet;
                        $options[] = ['id' => $id, 'name' => $name];
                    }
                }
                return $options;
            }

            // If content is an associative array (like judet-List key=>value)
            if (is_array($content) && !isset($content[0])) {
                $options = [];
                foreach ($content as $name => $id) {
                    $options[] = ['id' => $id, 'name' => $name];
                }
                return $options;
            }

            // If it's already in option format, return as-is
            return is_array($content) ? $content : [];
        } else {
            throw new Exception("API Error: status is false from " . $endpoint);
        }
    }

    // Fallback: if response is directly an array
    return is_array($data) ? $data : [];
}

// Fetch both location and category options
try {
    // Use the configured locations endpoint provided above
    $locationOptions = fetchOptionsFromApi($locationsEndpoint);
    $categoryOptions = fetchOptionsFromApi($risksValEndpoint);
    $apiError = null;
} catch (Exception $e) {
    $apiError = $e->getMessage();
    $locationOptions = [];
    $categoryOptions = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    // Read the form fields early so uploads won't affect slug generation
    $title = trim($_POST['title'] ?? '');
    $mainText = trim($_POST['mainText'] ?? '');
    $locationId = $_POST['locationId'] ?? '';
    $riskCategory = $_POST['riskCategory'] ?? '';

    // Process PDF uploads (up to 10 files)
    $savedFiles = [];
    $uploadResults = [];
    if (!empty($_FILES['pdf_files'])) {
        $files = $_FILES['pdf_files'];
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;
        
        // Limit to 10 PDFs
        $fileCount = min($fileCount, 10);
        
        for ($i = 0; $i < $fileCount; $i++) {
            // Rebuild single file array for savePdfUpload
            $singleFile = [
                'name' => is_array($files['name']) ? $files['name'][$i] : $files['name'],
                'type' => is_array($files['type']) ? $files['type'][$i] : $files['type'],
                'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                'error' => is_array($files['error']) ? $files['error'][$i] : $files['error'],
                'size' => is_array($files['size']) ? $files['size'][$i] : $files['size'],
            ];
            
            // Skip if no file or error
            if ($singleFile['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            
            // Temporarily override $_FILES for savePdfUpload
            $_FILES['pdf_file'] = $singleFile;
            $result = savePdfUpload('pdf_file', ['upload_dir' => __DIR__ . '/../uploads', 'max_size' => 15 * 1024 * 1024]);
            $uploadResults[] = $result;
            
            if (!empty($result['success'])) {
                $savedFiles[] = $result['filename'];
            }
        }
    }

    // Handle optional single post image upload (saved to /articles/src)
    $image = '';
    if (!empty($_FILES['post_image']) && isset($_FILES['post_image']['error']) && $_FILES['post_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $img = $_FILES['post_image'];
        if ($img['error'] === UPLOAD_ERR_OK) {
            // basic size check (5 MB)
            if ($img['size'] > 5 * 1024 * 1024) {
                $uploadResults[] = ['success' => false, 'message' => 'Image exceeds 5MB limit.'];
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($img['tmp_name']);
                if (strpos($mime, 'image/') !== 0) {
                    $uploadResults[] = ['success' => false, 'message' => 'Uploaded file is not an image. Detected: ' . $mime];
                } else {
                    $imagesDir = __DIR__ . '/../articles/src';
                    if (!is_dir($imagesDir)) {
                        mkdir($imagesDir, 0755, true);
                    }
                    // sanitize original name and make unique
                    $originalImg = basename($img['name']);
                    $cleanImg = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalImg);
                    $uniqueImg = sprintf("%s_%s_%s", date('YmdHis'), bin2hex(random_bytes(6)), $cleanImg);
                    $dest = rtrim($imagesDir, '/\\') . DIRECTORY_SEPARATOR . $uniqueImg;
                    if (move_uploaded_file($img['tmp_name'], $dest)) {
                        @chmod($dest, 0644);
                        // Use root-relative path so article pages under /journalism/ resolve correctly
                        $image = '/articles/src/' . $uniqueImg;
                        $uploadResults[] = ['success' => true, 'message' => 'Image uploaded successfully.', 'filename' => $uniqueImg, 'type' => 'image'];
                    } else {
                        $uploadResults[] = ['success' => false, 'message' => 'Failed to move uploaded image.', 'type' => 'image'];
                    }
                }
            }
        } else {
            $uploadResults[] = ['success' => false, 'message' => 'Image upload error code: ' . $img['error'], 'type' => 'image'];
        }
    }

    // If publishing or saving draft, create a PHP article file in /articles
    if (in_array($action, ['publish', 'draft', 'upload_pdf'], true)) {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($title)));
        if (!$slug) {
            $slug = 'article-' . time();
        }
        $fileName = $slug . '.php';
        $articlesDir = __DIR__ . '/../articles';
        if (!is_dir($articlesDir)) {
            mkdir($articlesDir, 0755, true);
        }
        $filePath = $articlesDir . DIRECTORY_SEPARATOR . $fileName;

        // Build a short summary from the article body
        $plain = trim(strip_tags($mainText));
        $summary = mb_substr($plain, 0, 160);
        $link = './journalism/article.php?slug=' . $slug;

        $content = "<?php\n";
        $content .= "// Generated article file\n";
        $content .= "\$title = " . var_export($title, true) . ";\n";
        $content .= "\$summary = " . var_export($summary, true) . ";\n";
        $content .= "\$image = " . var_export($image, true) . ";\n";
        $content .= "\$link = " . var_export($link, true) . ";\n";
        $content .= "\$body = " . var_export($mainText, true) . ";\n";
        $content .= "?>\n";

        // Write file (overwrite if exists)
        $written = file_put_contents($filePath, $content) !== false;
    }

    // Prepare payload for API
    $payload = [
        'info' => $mainText,
        'satRel' => $locationId,
        // 'type_risk' should be the chosen risk id (validate below)
        'type_risk' => $riskCategory,
        'arrayStringNumeFisiere' => $savedFiles,
    ];

    // Validate selected location and risk against fetched options
    $validLocation = false;
    foreach ($locationOptions as $opt) {
        if ((string)($opt['id'] ?? '') === (string)$locationId) { $validLocation = true; break; }
    }
    $validRisk = false;
    foreach ($categoryOptions as $opt) {
        if ((string)($opt['id'] ?? '') === (string)$riskCategory) { $validRisk = true; break; }
    }

    $apiStatus = null;
    if (!$validLocation) {
        $apiStatus = ['success' => false, 'message' => 'Invalid location selected. Please choose a valid location.'];
    } elseif (!$validRisk) {
        $apiStatus = ['success' => false, 'message' => 'Invalid risk category selected. Please choose a valid category.'];
    } else {
        // Send POST to API endpoint (JSON)
        $ch = curl_init($apiEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $apiResp = curl_exec($ch);
        $apiErr = curl_error($ch) ?: null;
        $apiCode = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: null;
        curl_close($ch);

        // Try decode response for friendly message
        $decoded = null;
        if ($apiResp) {
            $decoded = json_decode($apiResp, true);
        }
        if ($apiErr) {
            $apiStatus = ['success' => false, 'message' => 'API connection error: ' . $apiErr];
        } elseif ($apiCode && $apiCode >= 400) {
            $apiStatus = ['success' => false, 'message' => 'API error HTTP ' . $apiCode];
        } elseif (is_array($decoded) && isset($decoded['status'])) {
            // API returns structured object
            $apiStatus = ['success' => (bool)$decoded['status'], 'message' => isset($decoded['content']) ? (is_string($decoded['content']) ? $decoded['content'] : json_encode($decoded['content'])) : $apiResp];
        } else {
            // Fallback: treat any HTTP 200 as success
            $apiStatus = ['success' => true, 'message' => 'API responded: ' . ($apiResp ?: 'OK')];
        }
    }

    $formResult = [
        'written' => $written ?? false,
        'article_path' => ($written ?? false) ? ('articles/' . $fileName) : null,
        'upload' => $uploadResult,
        'api_status' => $apiStatus,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post - Infrastructure Gap</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #ffddc3; /* Light, editorial background */
            padding: 30px 0;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffefe2ff; 
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .header {
            border-bottom: 2px solid #DD2200;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            color: #111;
            font-size: 1.8em;
        }
        .btn-back {
            text-decoration: none;
            color: #DD2200;
            padding: 8px 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: 0.2s;
        }
        .btn-back:hover {
            background: #f0f0f0;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            font-size: 1.1em;
        }
        input[type="text"], 
        textarea, 
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            font-family: inherit;
        }
        input[type="text"]:focus, 
        textarea:focus, 
        select:focus {
            outline: 2px solid #e74c3c; /* Highlight color from journalism site */
            border-color: #e74c3c;
        }
        #mainText {
            min-height: 400px; /* Large writing area */
            resize: vertical;
        }
        
        /* File Upload Area */
        .file-upload-section {
            display: flex;
            gap: 15px;
            align-items: center;
            padding: 15px;
            border: 1px dashed #e74c3c;
            border-radius: 6px;
            background: #fffafa;
        }
        #uploadedFilesList {
            font-size: 0.9em;
            color: #666;
            margin-top: 10px;
            min-height: 20px;
            padding: 5px;
            border: 1px solid #eee;
        }

        /* Buttons */
        .btn-actions {
            margin-top: 30px;
            text-align: right;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
            font-weight: bold;
            margin-left: 10px;
        }
        .btn-primary {
            background: #e74c3c; /* Save/Publish button */
            color: white;
        }
        .btn-primary:hover {
            background: #c0392b;
        }
        .btn-secondary {
            background: #ccc; /* Draft/Cancel button */
            color: #333;
        }
        .btn-secondary:hover {
            background: #bbb;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>✍️ Create New Article Post</h1>
            <a href="../index.php" class="btn-back">← Home</a>
        </div>

        <?php if (!empty($apiError)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;">
                <strong>⚠️ API Server Error:</strong>
                <pre style="margin-top: 10px; font-size: 0.9em; overflow-x: auto;"><?php echo htmlspecialchars($apiError); ?></pre>
                <small>Please ensure the API server is running at <code><?php echo htmlspecialchars($risksValEndpoint); ?></code></small>
            </div>
        <?php endif; ?>

        <form id="postForm" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title / Headline</label>
                <input type="text" id="title" name="title" placeholder="Enter a compelling title for the report" required>
            </div>
            
            <div class="form-group">
                <label for="mainText">Article Body</label>
                <textarea id="mainText" name="mainText" placeholder="Start writing the full report here..." required></textarea>
            </div>

            <div class="form-group">
                <label for="locationId">Reference Location ID (Optional)</label>
                <select id="locationId" name="locationId">
                    <option value="">-- Select Location --</option>
                    <?php
                    if (!empty($locationOptions)) {
                        foreach ($locationOptions as $location) {
                            $id = $location['id'] ?? $location['val'] ?? null;
                            $name = $location['name'] ?? $location['label'] ?? $location['val'] ?? null;
                            if ($id !== null && $name !== null) {
                                echo '<option value="' . htmlspecialchars($id) . '">' . htmlspecialchars($name) . '</option>';
                            }
                        }
                    } else {
                        echo '<option value="">Unable to load locations</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="riskCategory">Risk Category (Optional)</label>
                <select id="riskCategory" name="riskCategory">
                    <option value="">-- Select Risk Category --</option>
                    <?php
                    if (!empty($categoryOptions)) {
                        foreach ($categoryOptions as $category) {
                            $id = $category['id'] ?? $category['val'] ?? null;
                            $name = $category['name'] ?? $category['label'] ?? $category['val'] ?? null;
                            if ($id !== null && $name !== null) {
                                echo '<option value="' . htmlspecialchars($id) . '">' . htmlspecialchars($name) . '</option>';
                            }
                        }
                    } else {
                        echo '<option value="">Unable to load categories</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Attach Media / Files</label>
                <div class="file-upload-section">
                    <!-- Existing client-side multi-file input (kept for other media types) -->
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <label for="pdf_files" style="font-weight:normal">Upload PDFs (optional, max 10 files, 15 MB each)</label>
                        <input type="file" name="pdf_files[]" id="pdf_files" accept="application/pdf" multiple>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;margin-left:16px;">
                    <label for="post_image" style="font-weight:normal">Upload Post Image (optional, max 5 MB)</label>
                    <input type="file" name="post_image" id="post_image" accept="image/*">
                </div>
                <input type="hidden" id="filesArray" name="files" value="[]">
                <div id="uploadedFilesList">No files uploaded yet.</div>

                <?php if (!empty($uploadResults)): ?>
                    <div id="uploadResults" style="margin-top:12px;transition:opacity .6s ease;opacity:1;display:block;">
                        <h4>Upload Results:</h4>
                        <?php foreach ($uploadResults as $idx => $result): ?>
                            <div style="margin:8px 0;padding:8px;border-left:3px solid <?php echo $result['success'] ? '#28a745' : '#dc3545'; ?>">
                                        <?php if (!empty($result['type']) && $result['type'] === 'image' && !empty($result['success'])): ?>
                                            <p class="<?php echo $result['success'] ? 'success' : 'error' ?>"><span id="imageUploadSuccess"><?php echo htmlspecialchars($result['message']); ?></span></p>
                                        <?php else: ?>
                                            <p class="<?php echo $result['success'] ? 'success' : 'error' ?>"><?php echo htmlspecialchars($result['message']); ?></p>
                                        <?php endif; ?>
                                <?php if ($result['success']): ?>
                                    <p>File: <code><?php echo htmlspecialchars($result['filename']); ?></code></p>
                                    <?php if (!empty($result['type']) && $result['type'] === 'image'): ?>
                                        <p><a href="<?php echo htmlspecialchars('../articles/src/' . rawurlencode($result['filename'])); ?>">View Image</a></p>
                                    <?php else: ?>
                                        <p><a href="<?php echo htmlspecialchars('../uploads/' . rawurlencode($result['filename'])); ?>">Download</a></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="btn-actions">
                <button type="submit" name="action" value="publish" class="btn btn-primary">Publish Post</button>
                <?php if (!empty($formResult['written'])): ?>
                    <span id="publishSuccess" style="margin-left:12px;color:#155724;background:#d4edda;padding:8px;border-radius:6px;border:1px solid #c3e6cb;font-weight:bold;display:inline-block;opacity:1;transition:opacity .6s ease;">Article successfully published</span>
                <?php endif; ?>
                <?php if (!empty($formResult['api_status'])): ?>
                    <?php $s = $formResult['api_status']; ?>
                    <span id="apiStatus" style="margin-left:12px;color:<?php echo $s['success'] ? '#155724' : '#721c24'; ?>;background:<?php echo $s['success'] ? '#d4edda' : '#f8d7da'; ?>;padding:8px;border-radius:6px;border:1px solid <?php echo $s['success'] ? '#c3e6cb' : '#f5c6cb'; ?>;font-weight:bold;display:inline-block;opacity:1;transition:opacity .6s ease;"><?php echo htmlspecialchars($s['message'] ?? ''); ?></span>
                <?php endif; ?>
            </div>
        </form>
    </div>

        <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Fade publish success
            var pub = document.getElementById('publishSuccess');
            if (pub) {
                setTimeout(function(){ pub.style.opacity = '0'; setTimeout(function(){ pub.style.display = 'none'; }, 700); }, 4000);
            }

            // Fade upload results
            var up = document.getElementById('uploadResults');
            if (up) {
                setTimeout(function(){ up.style.opacity = '0'; setTimeout(function(){ up.style.display = 'none'; }, 700); }, 5000);
            }
            // Fade image upload success specifically (if present)
            var imgSuccess = document.getElementById('imageUploadSuccess');
            if (imgSuccess) {
                setTimeout(function(){ imgSuccess.style.opacity = '0'; setTimeout(function(){ imgSuccess.style.display = 'none'; }, 700); }, 4000);
            }
            // Fade API status (create/post response)
            var apiStatus = document.getElementById('apiStatus');
            if (apiStatus) {
                setTimeout(function(){ apiStatus.style.opacity = '0'; setTimeout(function(){ apiStatus.style.display = 'none'; }, 700); }, 4500);
            }
        });
        </script>

        </body>
        </html>