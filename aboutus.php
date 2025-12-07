<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despre Noi – Mandarina Descojită Press</title>
    <link rel="stylesheet" href="style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
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

    <!-- Hero Section -->
    <section class="hero about-hero">
        <div class="hero-content">
            <h1>Cine Suntem Noi?</h1>
            <p></p>
        </div>
    </section>

    <main class="content">

        <!-- Mission -->
        <section class="featured">
            <h2>Misiunea Noastră</h2>
            <div class="about-block">
                <p>
                    Suntem o echipă de jurnaliști pasionați, care luptă zilnic pentru transparență,
                    corectitudine și informarea publicului. Credem într-o presă liberă, neafiliată politic,
                    dedicată interesului public.
                </p>
            </div>
        </section>

        <!-- Values -->
        <section class="featured">
            <br></br>
            <h2>Valorile Noastre</h2>
            <div class="grid about-grid">
                <div class="card">
                    <br></br>
                    <h3>Integritate</h3>
                    <p>Respectăm adevărul și principiile jurnalismului profesionist.</p>
                    <br></br>
                    </div>

                <div class="card">
                    <br></br>
                    <h3>Transparență</h3>
                    <p>Oferim informații verificabile și ușor de înțeles.</p>
                    <br></br>
                </div>

                <div class="card">
                    <br></br>
                    <h3>Corectitudine</h3>
                    <p>Relatăm faptele echilibrat, fără influențe externe.</p>
                    <br></br>
                </div>

                <div class="card">
                    <br></br>
                    <h3>Responsabilitate</h3>
                    <p>Înțelegem impactul informației și tratăm fiecare subiect cu seriozitate.</p>
                    <br></br>
                </div>
            </div>
        </section>

        <!-- Team -->
        <section class="featured">
             <br></br>
            <h2>Echipa Noastră</h2>
            <br></br>
            <div class="grid team-grid">

                <div class="card team-card">
                    <img src="il.jpeg" alt="">
                    <b>Rodilă Ilinca</b>
                    <p>Frontend, Șofer</p>
                </div>

                <div class="card team-card">
                    <img src="is.jpeg" alt="">
                    <b>Silaghi Iasmina</b>
                    <p>Frontend</p>
                </div>

                <div class="card team-card">
                    <img src="rm.jpeg" alt="">
                    <b>Illyeș Remus</b>
                    <p>Pitch, Business</p>
                </div>

                 <div class="card team-card">
                    <img src="mk.jpeg" alt="">
                    <b>Mekker David</b>
                    <p>Team Leader, Backend</p>
                </div>

                <div class="card team-card">
                    <img src="sz.jpeg" alt="">
                    <b>Balint Szabolcs</b>
                    <p>Backend</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Mandarina Descojită.</p>
    </footer>

    <script>
        const toggle = document.getElementById("menuToggle");
        const menu = document.getElementById("navMenu");

        toggle.addEventListener("click", () => {
            menu.classList.toggle("open");
        });
    </script>

</body>

</html>