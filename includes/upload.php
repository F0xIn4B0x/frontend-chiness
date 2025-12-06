<?php
/**
 * savePdfUpload()
 * Handles a single PDF upload from an HTML form `input` with given name.
 * Validates file type, size, and moves file to `uploads/` directory.
 *
 * Usage:
 *  require 'includes/upload.php';
 *  $result = savePdfUpload('pdf_file');
 *
 * Returns array:
 *  [
 *    'success' => bool,
 *    'message' => string,
 *    'filename' => string|null, // stored filename relative to uploads/ on success
 *  ]
 */

function savePdfUpload(string $inputName, array $options = []): array
{
    $uploadDir = $options['upload_dir'] ?? __DIR__ . '/../uploads';
    $maxSize = $options['max_size'] ?? 10 * 1024 * 1024; // 10 MB default

    if (!isset($_FILES[$inputName])) {
        return ['success' => false, 'message' => 'No file uploaded.'];
    }

    $file = $_FILES[$inputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $msg = match ($file['error']) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Uploaded file is too large.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            default => 'Unknown upload error.',
        };
        return ['success' => false, 'message' => $msg];
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File exceeds maximum allowed size.'];
    }

    // Basic MIME check via finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if ($mime !== 'application/pdf') {
        return ['success' => false, 'message' => 'Only PDF files are allowed. Detected: ' . $mime];
    }

    // Ensure upload directory exists and is writable
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory.'];
        }
    }
    if (!is_writable($uploadDir)) {
        return ['success' => false, 'message' => 'Upload directory is not writable.'];
    }

    // Build a safe unique filename: timestamp + random + original basename sanitized
    $original = basename($file['name']);
    $clean = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
    $unique = sprintf("%s_%s_%s", date('YmdHis'), bin2hex(random_bytes(6)), $clean);

    $destination = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $unique;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }

    // Optionally set permissions
    @chmod($destination, 0644);

    return [
        'success' => true,
        'message' => 'File uploaded successfully.',
        'filename' => $unique,
    ];
}
