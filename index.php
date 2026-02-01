<?php
// Iniciar sesión y headers ANTES de cualquier output
require_once __DIR__ . '/cms/includes/config.php';
require_once __DIR__ . '/cms/includes/security.php';
Security::setSecurityHeaders();

// Cargar últimos 3 posts del blog
require_once __DIR__ . '/cms/includes/database.php';
require_once __DIR__ . '/cms/includes/blog.php';

$blogModel = new Blog();
$recentPosts = $blogModel->getAll('published');
$recentPosts = array_slice($recentPosts, 0, 3); // Solo los 3 más recientes
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Petersen - Tu plataforma de soluciones inteligentes. Herramientas profesionales y soluciones industriales desde 1930.">
    <title>Petersen | Soluciones Inteligentes</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
    <section class="hero">
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="assets/video/hero.mp4" type="video/mp4">
        </video>
    </section>

    <!-- CTA Buttons Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ponemos las herramientas correctas en las manos correctas, desde 1930.</h2>
            <div class="cta-buttons">
                <a href="#rubros" class="btn-cta btn-cta-outline">RUBROS</a>
                <a href="/aliados" class="btn-cta btn-cta-outline">MARCAS</a>
                <a href="https://tiendapetersen.com.py" target="_blank" rel="noopener noreferrer" class="btn-cta btn-cta-primary">COMPRAR ONLINE</a>
            </div>
        </div>
    </section>

    <!-- Historia Section -->
    <section class="historia-section" id="historia">
        <div class="historia-container">
            <div class="historia-content">
                <h2 class="historia-title">Una historia creciendo con el país</h2>
                <p class="historia-text">Somos una plataforma de soluciones inteligentes, confiables y profesionales, porque buscamos acompañar el crecimiento de los profesionales, asesorando a nuestros clientes para rentabilizar su inversión. Innovando desde 1930.</p>
                <a href="/quienes-somos" class="btn-historia">CONOCE NUESTRA HISTORIA</a>
            </div>
            <div class="historia-slider">
                <div class="slider-container">
                    <div class="slider-track">
                        <div class="slide active">
                            <img src="assets/images/nuevafachada.jpg" alt="Petersen">
                        </div>
                        <div class="slide">
                            <img src="assets/images/nuevafachada.jpg" alt="Petersen">
                        </div>
                        <div class="slide">
                            <img src="assets/images/nuevafachada.jpg" alt="Petersen">
                        </div>
                        <div class="slide">
                            <img src="assets/images/nuevafachada.jpg" alt="Petersen">
                        </div>
                    </div>
                </div>
                <div class="slider-dots">
                    <span class="dot active" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                    <span class="dot" data-slide="3"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Rubros Section -->
    <section class="rubros-section" id="rubros">
        <div class="rubros-container">
            <div class="section-title">
                <h2>Nuestros Rubros</h2>
            </div>
            
            <div class="rubros-carousel">
                <button class="carousel-arrow carousel-prev" aria-label="Anterior">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                
                <div class="rubros-track">
                    <a href="/petersen2025/division-metalurgica" class="rubro-card">
                        <img src="assets/images/icons/metalurgica.png" alt="Metalúrgica">
                        <span>METALÚRGICA</span>
                    </a>
                    <a href="/petersen2025/division-industrial" class="rubro-card">
                        <img src="assets/images/icons/industrial.png" alt="Industria">
                        <span>INDUSTRIA</span>
                    </a>
                    <a href="/petersen2025/division-ferreteria" class="rubro-card">
                        <img src="assets/images/icons/ferreteria.png" alt="Ferretería">
                        <span>FERRETERÍA</span>
                    </a>
                    <a href="/petersen2025/division-mecanica" class="rubro-card">
                        <img src="assets/images/icons/mecanica.png" alt="Mecánica">
                        <span>MECÁNICA</span>
                    </a>
                    <a href="/petersen2025/division-bosque-y-jardin" class="rubro-card">
                        <img src="assets/images/icons/forestal.png" alt="Bosque &amp; Jardín">
                        <span>BOSQUE &amp; JARDÍN</span>
                    </a>
                    <a href="/petersen2025/division-construccion" class="rubro-card">
                        <img src="assets/images/icons/construccion.png" alt="Construcción">
                        <span>CONSTRUCCIÓN</span>
                    </a>
                </div>
                
                <button class="carousel-arrow carousel-next" aria-label="Siguiente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Aliados Section -->
    <section class="aliados-section">
        <div class="aliados-container">
            <div class="section-title">
                <h2>Aliados</h2>
            </div>
            
            <div class="aliados-carousel">
                <button class="carousel-arrow carousel-prev" aria-label="Anterior">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                
                <div class="aliados-track">
                    <div class="aliado-card">
                        <img src="assets/images/logos/truper.png" alt="Truper">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/gladiator.png" alt="Gladiator">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/gedore.png" alt="Gedore">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/energy.png" alt="Energy">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/esab.png" alt="ESAB">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/chesy.png" alt="Chesy">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/conarco.png" alt="Conarco">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/driven.png" alt="Driven">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/ecef.png" alt="ECEF">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/emax.png" alt="Emax">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/forestgarden.png" alt="Forest Garden">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/genebre.png" alt="Genebre">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/hydrate.png" alt="Hydrate">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/hyundai.png" alt="Hyundai">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/neo.png" alt="Neo">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/oleomac.png" alt="Oleo Mac">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/spiraxsarco.png" alt="Spirax Sarco">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/starret.png" alt="Starret">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/sumig.png" alt="Sumig">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/thor.png" alt="Thor">
                    </div>
                    <div class="aliado-card">
                        <img src="assets/images/logos/tyrolit.png" alt="Tyrolit">
                    </div>
                </div>
                
                <button class="carousel-arrow carousel-next" aria-label="Siguiente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Campañas Section 
    <section class="campanas-section">
        <div class="campanas-container">
            <div class="section-title">
                <h2>Campañas</h2>
            </div>
            
            <div class="campanas-carousel">
                <button class="carousel-arrow carousel-prev" aria-label="Anterior">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                
                <div class="campana-content">
                    <div class="campana-image">
                        <img src="assets/images/feria.png" alt="La Feria 2025">
                    </div>
                    <div class="campana-info">
                        <h4>La Feria 2025</h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    </div>
                </div>
                
                <button class="carousel-arrow carousel-next" aria-label="Siguiente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    -->

    <!-- Blog Section -->
    <section class="blog-section">
        <div class="container">
            <div class="section-title">
                <h2>Blog</h2>
            </div>
            <?php if (!empty($recentPosts)): ?>
            <div class="blog-grid">
                <?php foreach ($recentPosts as $post): ?>
                <article class="blog-card">
                    <a href="blog-post.php?slug=<?php echo urlencode($post['slug']); ?>">
                        <div class="blog-card-image">
                            <?php if ($post['featured_image']): ?>
                                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <?php else: ?>
                                <img src="assets/images/blog/default.jpg" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="blog-card-content">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <span class="blog-date">
                                <?php 
                                $date = new DateTime($post['published_at']);
                                echo $date->format('d \d\e F, Y'); 
                                ?>
                            </span>
                            <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="blog-cta">
                <a href="blog.php" class="btn btn-primary">Ver blog</a>
            </div>
        </div>
    </section>

    <!-- Servicios Destacados Section -->
    <section class="servicios-destacados-section">
        <div class="servicios-destacados-container">
            <div class="section-title">
                <h2>Servicios Destacados</h2>
            </div>
            
            <div class="servicios-destacados-grid">
                <div class="servicio-destacado-card">
                    <img src="assets/images/asesoria.png" alt="Asesoría Personalizada">
                    <div class="servicio-destacado-overlay">
                        <h4>Asesoría Personalizada</h4>
                    </div>
                </div>
                <div class="servicio-destacado-card">
                    <img src="assets/images/soportetecnico.png" alt="Soporte Técnico">
                    <div class="servicio-destacado-overlay">
                        <h4>Soporte Técnico</h4>
                    </div>
                </div>
                <div class="servicio-destacado-card">
                    <img src="assets/images/postventa.png" alt="Post Venta">
                    <div class="servicio-destacado-overlay">
                        <h4>Post Venta</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <section class="map-section">
        <div class="container">
            <div class="section-title">
                <h2>Sucursales</h2>
            </div>
            <div class="map-container">
                <div id="indexSucursalesMap" style="width: 100%; height: 400px; border-radius: 12px;"></div>
            </div>
            <div class="map-cta">
                <a href="/sucursales" class="btn btn-primary">Ver Sucursales</a>
            </div>
        </div>
    </section>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sucursales = [
            { name: "Casa Central", lat: -25.2867, lng: -57.6470, url: "https://maps.app.goo.gl/dP77fbZNrobLW9mL7" },
            { name: "Ciudad del Este", lat: -25.5097, lng: -54.6111, url: "https://maps.app.goo.gl/WgA5gcxoznocdwRo9" },
            { name: "Eusebio Ayala", lat: -25.2989, lng: -57.5856, url: "https://maps.app.goo.gl/UE1BNfNGgsv7o8tU9" },
            { name: "San Lorenzo", lat: -25.3389, lng: -57.5094, url: "https://maps.app.goo.gl/NZpY3F7pQ4NqVpFa9" },
            { name: "Mariano Roque Alonso", lat: -25.2019, lng: -57.5347, url: "https://maps.app.goo.gl/KLoYwSQk1uQwiETE8" },
            { name: "Acceso Sur", lat: -25.3564, lng: -57.5533, url: "https://maps.app.goo.gl/5RQ3FcTWawFaqrMj6" }
        ];
        
        const mapElement = document.getElementById('indexSucursalesMap');
        if (mapElement) {
            const map = L.map('indexSucursalesMap').setView([-25.3, -56.5], 7);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            const bounds = [];
            
            const blueIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            
            sucursales.forEach(sucursal => {
                const marker = L.marker([sucursal.lat, sucursal.lng], { icon: blueIcon }).addTo(map);
                marker.bindPopup(`<strong>${sucursal.name}</strong><br><a href="${sucursal.url}" target="_blank" style="color: #EE7124;">Ver en Google Maps</a>`);
                bounds.push([sucursal.lat, sucursal.lng]);
            });
            
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [30, 30] });
            }
        }
    });
    </script>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
