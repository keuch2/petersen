<?php
// Iniciar sesión y headers ANTES de cualquier output
require_once 'cms/includes/config.php';
require_once 'cms/includes/security.php';
Security::setSecurityHeaders();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aliados de Petersen - Marcas líderes mundiales en herramientas y soluciones profesionales.">
    <title>Aliados | Petersen</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>Aliados</h1>
        </div>
    </div>

    <!-- Aliados Content -->
    <section style="padding: 60px 0;">
        <div class="container">
            <div class="aliados-full-grid">
                <div class="aliado-card">
                    <img src="assets/images/logos/conarco.png" alt="Conarco">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/esab.png" alt="ESAB">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/okesab.jpg" alt="OK ESAB">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/sumig.png" alt="Sumig">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/thor.png" alt="Thor">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/gladiator.png" alt="Gladiator">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/truper.png" alt="Truper">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/neo.png" alt="Neo">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/forestgarden.png" alt="Forest & Garden">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/energy.png" alt="Energy">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/starret.png" alt="Starrett">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/genebre.png" alt="Genebre">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/oleomac.png" alt="Oleo-Mac">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/chesy.png" alt="Chesy">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/driven.png" alt="Driven">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/spiraxsarco.png" alt="Spirax Sarco">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/emax.png" alt="EMAX">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/dunlop.jpg" alt="Dunlop">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/sullair.jpg" alt="Sullair">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/termocril.jpg" alt="Termocril">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/hyundai.png" alt="Hyundai">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/eutetic.jpg" alt="Castolin Eutectic">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/ecef.png" alt="ECEF">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/gedore.png" alt="Gedore">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/gedore-red.jpg" alt="Gedore Red">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/maskin.jpg" alt="Maskin">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/klinger.jpg" alt="Klinger">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/rexnord.jpg" alt="Rexnord">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/grundfos.jpg" alt="Grundfos">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/lince.jpg" alt="Lince">
                </div>
                <div class="aliado-card">
                    <img src="assets/images/logos/tyrolit.png" alt="Tyrolit">
                </div>
            </div>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
