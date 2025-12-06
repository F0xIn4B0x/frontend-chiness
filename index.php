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
                <li><a href="#">Jurnalisti</a></li>
                <li><a href="#">Date Analitice</a></li>
                <li><a href="#">Login</a></li>
                <li><a href="#">Despre Noi</a></li>
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
                <article class="card">
                    <img src="https://via.placeholder.com/400x250" alt="">
                    <h3>Story Title One</h3>
                    <p>Short summary of this article goes here.</p>
                </article>

                <article class="card">
                    <img src="https://via.placeholder.com/400x250" alt="">
                    <h3>Story Title Two</h3>
                    <p>Short summary of this article goes here.</p>
                </article>

                <article class="card">
                    <img src="https://via.placeholder.com/400x250" alt="">
                    <h3>Story Title Three</h3>
                    <p>Short summary of this article goes here.</p>
                </article>
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
        <p>&copy; 2025 Daily Report. All rights reserved.</p>
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