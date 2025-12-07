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
            <strong>MANDARINA DESCOJITĂ PRESS</strong> este o platformă de jurnalism investigativ apolitic dedicată protejării societății românești de la alunecarea spre dezastru.
            Ne concentrăm pe expunerea lipsurilor critice din infrastructură, a deficiențelor din sistemele de prevenție și a problemelor majore care afectează în special comunitățile defavorizate ale țării.
        </p>

        <p>
            Credem că adevărul expus transparent și responsabil poate salva vieți. Documentăm probleme reale,
            semnalăm riscurile ignorate și oferim date clare care să susțină luarea unor decizii informate —
            atât de către cetățeni, cât și de către instituțiile responsabile.
        </p>

        <p>
            Ne ghidăm după un singur principiu: <em>interesul public</em> — fără agende politice, fără manipulări,
            fără compromisuri. De aceea ne inspirăm din rigoarea și etica publicațiilor independente din România,
            precum Recorder și Snoop, suntem deschiși să colaborăm cu redacții și jurnaliști care împărtășesc
            aceleași valori și aceeași responsabilitate față de societate.
        </p>

        <p>
            Prin investigații solide și implicare directă în comunități, lucrăm pentru o Românie mai sigură,
            mai transparentă și mai pregătită să prevină tragediile ce pot fi evitate.
        </p>
    </div>
</section>

<!-- Values -->
<section class="featured">
    <h2>Valorile Noastre</h2>

    <div class="grid about-grid">

        <div class="card">
            <h3>Integritate</h3>
            <p>
                Ne ghidăm exclusiv după interesul public. Adevărul, etica și corectitudinea
                sunt fundamentul fiecărei investigații pe care o realizăm.
            </p>
        </div>

        <div class="card">
            <h3>Transparență</h3>
            <p>
                Punem la dispoziția societății date clare, verificabile și ușor de urmărit,
                astfel încât realitatea despre infrastructură, riscuri și lipsuri să nu mai
                poată fi ascunsă.
            </p>
        </div>

        <div class="card">
            <h3>Corectitudine</h3>
            <p>
                Suntem independenți și apolitici. Prezentăm faptele exact așa cum sunt,
                fără influențe, fără presiuni și fără compromisuri.
            </p>
        </div>

        <div class="card">
            <h3>Responsabilitate</h3>
            <p>
                Înțelegem că informarea poate salva vieți. De aceea tratăm fiecare subiect,
                în special din zonele defavorizate, cu seriozitate și cu dorința reală
                de a preveni tragedii.
            </p>
        </div>

    </div>
</section>

        <!-- Team -->
        <section class="featured">
             <br></br>
            <h2>Echipa tehnică</h2>
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