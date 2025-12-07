<?php
/**
 * Article Detail Viewer
 * Displays a single article with all its data
 * Usage: article.php?slug=article-slug
 */

$slug = $_GET['slug'] ?? null;

// Security: validate slug format
if (!$slug || !preg_match('/^[A-Za-z0-9-]+$/', $slug)) {
    http_response_code(400);
    echo "<h1>Invalid Article</h1>";
    exit;
}

// Load article data
$articleFile = __DIR__ . '/../articles/' . $slug . '.php';
if (!file_exists($articleFile)) {
    http_response_code(404);
    echo "<h1>Article Not Found</h1>";
    exit;
}

// Include article to get variables
$title = $summary = $image = $link = $body = $files = null;
include $articleFile;

// If no title, article data is corrupted
if (!$title) {
    http_response_code(500);
    echo "<h1>Article Data Error</h1>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - Daily Report</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <nav class="navbar">
        <div class="logo">MANDARINA DESCOJITĂ PRESS</div>
        <img src="logo.png" alt="logo" />
        </div>
    </nav>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #ffddc3;
            color: #1a1a1a;
            line-height: 1.7;
        }
        
        /* Navigation */
    .navbar {
    background: #DD2200;
    color: #fff;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
}

.logo {
    font-size: 1.2rem;
    font-weight: 700;
}

/* header logo (sibling <img> in .navbar) */
.navbar img {
    height: 50px;       /* desired visible height */
    width: auto;        /* preserve aspect ratio */
    max-height: 60px;   /* caps on very large images */
    object-fit: contain;
    margin-right: 900px; /* space between logo and site title */

}
        .navbar-content {
            max-width: 500px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 50px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .article-header {
            border-bottom: 3px solid #DD2200;
            padding-bottom: 30px;
            margin-bottom: 40px;
        }
        
        h1 {
            font-size: 2.8em;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .article-meta {
            display: flex;
            gap: 30px;
            font-size: 0.95em;
            color: #666;
            font-style: italic;
        }
        
        .article-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .article-featured-image {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 30px;
            display: block;
        }
        
        .article-body {
            font-size: 1.05em;
            line-height: 1.9;
            color: #333;
            margin-bottom: 40px;
        }
        
        .article-body p {
            margin-bottom: 20px;
            text-align: justify;
        }
        
        .article-body p:first-letter {
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .article-divider {
            border: none;
            height: 2px;
            background: linear-gradient(to right, transparent, #DD2200, transparent);
            margin: 40px 0;
        }
        
        .article-files {
            background: #f9f9f9;
            padding: 25px;
            border-left: 4px solid #DD2200;
            margin-top: 40px;
            border-radius: 4px;
        }
        
        .article-files h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .file-list {
            list-style: none;
            padding: 0;
        }
        
        .file-list li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .file-list li:last-child {
            border-bottom: none;
        }
        
        .file-list a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s;
        }
        
        .file-list a:hover {
            color: #c0392b;
            text-decoration: underline;
        }
        
        .footer-nav {
            display: flex;
            gap: 20px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .btn-back, .btn-top {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #DD2200;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: 0.2s;
            font-weight: 500;
        }
        
        .btn-back:hover, .btn-top:hover {
            background: #ff2600ff;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 25px;
                margin: 20px;
            }
            h1 {
                font-size: 2em;
            }
            .article-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    


    <div class="container">
        <article>
            <header class="article-header">
                <h1><?php echo htmlspecialchars($title); ?></h1>
                <div class="article-meta">
                    <span>Published on <?php echo date('F j, Y', filemtime($articleFile)); ?></span>
                    <span>Article ID: <code><?php echo htmlspecialchars($slug); ?></code></span>
                </div>
            </header>

            <?php if (!empty($image)): ?>
                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="article-featured-image">
            <?php endif; ?>

            <div class="article-body">
                <?php echo nl2br(htmlspecialchars($body ?? '')); ?>
            </div>

            <hr class="article-divider">

            <?php if (!empty($files) && is_array($files)): ?>
                <div class="article-files">
                    <h3> Attached Documents</h3>
                    <ul class="file-list">
                        <?php foreach ($files as $filename): ?>
                            <li>
                                <a href="<?php echo htmlspecialchars('../uploads/' . rawurlencode($filename)); ?>" target="_blank">
                                    <?php echo htmlspecialchars($filename); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="footer-nav">
                <a href="../index.php" class="btn-back">← Back to News</a>
            </div>
        </article>
    </div>
</body>
</html>
