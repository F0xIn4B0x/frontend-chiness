<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandarina Descojită Press</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Navigation -->
    <header class="navbar">
        <div class="logo">MANDARINA DESCOJITĂ PRESS</div>
        <img src="logo.png" alt="logo" />

        <!-- Mobile Menu Button -->
        <div class="menu-toggle" id="menuToggle">☰</div>

        <nav id="navMenu">
            <ul>
                <li><a href="/">Acasă</a></li>
                <li><a href="journalism/login.php">Jurnaliști</a></li>
                <li><a href="#">Date Analitice</a></li>
                <li><a href="aboutus.php">Despre Noi</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>Ipocrizia omoară România!</h1>
            <p>Informează-te! Ia măsuri!</p>
            <p>Acoperim următoarele orașe: </p>
           <marquee behavior="scroll" direction="Left" scrollamount="3"></marquee>
        </div>
    </section>

    <!-- Content -->
    <main class="content">

        <!-- Featured -->
        <section class="featured">
            <h2>Știri de ultima ora</h2>
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

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Mandarina Descojită.</p>
    </footer>

    <script>
        // Mobile navigation toggle
        const toggle = document.getElementById("menuToggle"); //!! PT MOBILE DA OVERLAP SI ARATA CA PULA TRB SA REZOLV
        const menu = document.getElementById("navMenu");

        toggle.addEventListener("click", () => {
            menu.classList.toggle("open");
        });

        // Populate marquee from API
        (async function populateMarquee(){
            const mq = document.querySelector('marquee');
            if (!mq) return;
            try {
                const res = await fetch('http://localhost:8080/locations');
                if (!res.ok) throw new Error('network');
                const json = await res.json();
                if (!json?.status || !Array.isArray(json.content)) throw new Error('invalid');
                const items = json.content.map(it => `${it.locatie} (${it.judeti})`);
                // duplicate for smoother continuous scroll
                mq.innerText = items.concat(items).join(' — ');
            } catch (e) {
                mq.innerText = 'error!';
                console.error('marquee load error:', e);
            }
        })();
    </script>

</body>
</html>