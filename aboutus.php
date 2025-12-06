<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despre Noi – Mandarina Decojita Press</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Navigation -->
    <header class="navbar">
        <div class="logo">MANDARINA DECOJITA PRESS</div>

        <div class="menu-toggle" id="menuToggle">☰</div>

        <nav id="navMenu">
            <ul>
                <li><a href="#">Acasa</a></li>
                <li><a href="#">Jurnalisti</a></li>
                <li><a href="#">Date Analitice</a></li>
                <li><a href="#">Login</a></li>
                <li><a href="#" class="active">Despre Noi</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero about-hero">
        <div class="hero-content">
            <h1>Cine Suntem Noi?</h1>
            <p> </p>
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
                    <h3>Integritate</h3>
                    <p>Respectăm adevărul și principiile jurnalismului profesionist.</p>
                </div>

                <div class="card">
                    <h3>Transparență</h3>
                    <p>Oferim informații clare, verificabile și ușor de înțeles.</p>
                </div>

                <div class="card">
                    <h3>Corectitudine</h3>
                    <p>Relatăm faptele echilibrat, fără influențe externe.</p>
                </div>

                <div class="card">
                    <h3>Responsabilitate</h3>
                    <p>Înțelegem impactul informației și tratăm fiecare subiect cu seriozitate.</p>
                </div>
            </div>
        </section>

        <!-- Team -->
        <section class="featured">
             <br></br>
            <h2>Echipa Noastră</h2>

            <div class="grid team-grid">
                <div class="card team-card">
                    <img src="https://via.placeholder.com/300x300" alt="">
                    <h3>Andrei Popescu</h3>
                    <p>Redactor Șef</p>
                </div>

                <div class="card team-card">
                    <img src="https://via.placeholder.com/300x300" alt="">
                    <h3>Maria Ionescu</h3>
                    <p>Editor Politic</p>
                </div>

                <div class="card team-card">
                    <img src="https://via.placeholder.com/300x300" alt="">
                    <h3>Alex Dinu</h3>
                    <p>Reporter Investigații</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Mandarina Decojita.</p>
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