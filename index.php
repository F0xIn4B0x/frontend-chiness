<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report – News</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Navigation -->
    <header class="navbar">
        <div class="logo">MANDARINA DECOJITA PRESS</div>

        <!-- Mobile Menu Button -->
        <div class="menu-toggle" id="menuToggle">☰</div>

        <nav id="navMenu">
            <ul>
                <li><a href="#">Acasa</a></li>
                <li><a href="journalism/login.php">Jurnalisti</a></li>
                <li><a href="#">Date Analitice</a></li>
                <li><a href="aboutus.php">Despre Noi</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>Breaking News Headline Goes Here</h1>
            <p>A brief description of the top story, offering key details to convert the reader’s attention.</p>
            <a href="#" class="btn">Read Full Story</a>
        </div>
    </section>

    <!-- Content -->
    <main class="content">

        <!-- Featured -->
        <section class="featured">
            <h2>Top Stories</h2>
            <div class="grid">
                <?php
                // Load all PHP files from the `articles` folder and render a card for each.
                $articleFiles = glob(__DIR__ . '/articles/*.php');

                // Optional: sort by modification time (newest first)
                usort($articleFiles, function ($a, $b) {
                    return filemtime($b) <=> filemtime($a);
                });

                foreach ($articleFiles as $file) {
                    // Include the article inside a function scope so variables from the
                    // included file don't leak into the global scope. Each article
                    // file should set variables like $title, $summary, $image, $link.
                    $article = (function ($path) {
                        $title = $summary = $image = $link = null;
                        include $path;
                        return compact('title', 'summary', 'image', 'link');
                    })($file);

                    $title = $article['title'] ?? 'Untitled Story';
                    $summary = $article['summary'] ?? '';
                    $image = $article['image'] ?? 'https://via.placeholder.com/400x250';
                    $link = $article['link'] ?? '#';
                    ?>

                    <article class="card">
                        <a href="<?php echo htmlspecialchars($link); ?>">
                            <h3><?php echo htmlspecialchars($title); ?></h3>
                            <p><?php echo htmlspecialchars($summary); ?></p>
                        </a>
                    </article>

                <?php
                }
                ?>
            </div>
        </section>

        <!-- Latest -->
         <br></br>
        <section class="latest">
            <h2>Latest News</h2>
            <div class="latest-list">
                <div class="latest-item">
                    <h4>Latest Story Title</h4>
                    <p>Quick summary of a trending update.</p>
                </div>

                <div class="latest-item">
                    <h4>Another Breaking Update</h4>
                    <p>Brief description of the recent event.</p>
                </div>

                <div class="latest-item">
                    <h4>New Development in Tech</h4>
                    <p>Short description of tech news.</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Mandarina Decojita.</p>
    </footer>

    <script>
        // Mobile navigation toggle
        const toggle = document.getElementById("menuToggle"); //!! PT MOBILE DA OVERLAP SI ARATA CA PULA TRB SA REZOLV
        const menu = document.getElementById("navMenu");

        toggle.addEventListener("click", () => {
            menu.classList.toggle("open");
        });
    </script>

</body>
</html>