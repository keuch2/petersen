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
    <meta name="description" content="Conoce la historia de Petersen - Más de 90 años ofreciendo soluciones profesionales en Paraguay.">
    <title>Quienes Somos | Petersen</title>
    
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
            <h1>Quienes Somos</h1>
        </div>
    </div>

    <div class="quienes-somos-hero" style="background-image: url('assets/images/hero-quienessomos1.jpg');"></div>

    <!-- About Intro Section -->
    <section class="about-intro">
        <div class="container">
            <div class="about-intro-content">
                <div class="about-intro-text">
                    <p>Petersen es una empresa nacional que cuenta con una sólida trayectoria en el mercado ofreciendo soluciones profesionales y herramientas de trabajo.</p>
                    <p>Somos aliados de marcas líderes hace más de 90 años, ofrecemos soluciones industriales con marcas internacionales y servicios de primera línea, acompañando a nuestros clientes con la excelencia que nos caracteriza.</p>
                </div>
                <div class="about-video">
                    <iframe src="https://www.youtube.com/embed/ZGxPiKaIdHg" title="Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                   
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="timeline-section">
        <div class="container">
            <div class="section-title">
                <h2>Nuestra Historia</h2>
            </div>
            <p class="timeline-subtitle">Ponemos las herramientas correctas en las manos correctas, desde 1930.</p>
            
            <div class="timeline-carousel">
                <button class="timeline-nav timeline-prev">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <div class="timeline-track-wrapper">
                    <div class="timeline-track">
                        <div class="timeline-card">
                            <img src="assets/images/historia/1930.png" alt="1930">
                            <div class="timeline-card-overlay">
                                <span class="timeline-year">1930</span>
                                <p>Una tradición iniciada en 1930 y mantenida por cuatro generaciones</p>
                            </div>
                        </div>
                        <div class="timeline-card">
                            <img src="assets/images/historia/2006.png" alt="2006">
                            <div class="timeline-card-overlay">
                                <span class="timeline-year">2006</span>
                                <p>La división Ferretería se independiza, dando lugar al nacimiento de PETERSEN INDUSTRIA & HOGAR S.A. con un local en Asunción y otro en CDE.</p>
                            </div>
                        </div>
                        <div class="timeline-card">
                            <img src="assets/images/historia/2007.png" alt="2007">
                            <div class="timeline-card-overlay">
                                <span class="timeline-year">2007</span>
                                <p>Apertura de la Sucursal sobre Avda. Eusebio Ayala.</p>
                            </div>
                        </div>
                        <div class="timeline-card">
                            <img src="assets/images/historia/2009.png" alt="2009">
                            <div class="timeline-card-overlay">
                                <span class="timeline-year">2009</span>
                                <p>Apertura de la Sucursal San Lorenzo.</p>
                            </div>
                        </div>
                        <div class="timeline-card">
                            <img src="assets/images/historia/2015.png" alt="2015">
                            <div class="timeline-card-overlay">
                                <span class="timeline-year">2015</span>
                                <p>Apertura de nuestro Centro Logístico en Mariano Roque Alonso.</p>
                            </div>
                        </div>
                        <div class="timeline-card">
                            <img src="assets/images/historia/2017.png" alt="2017">
                            <div class="timeline-card-overlay">
                                <span class="timeline-year">2017</span>
                                <p>Apertura de la Sucursal Mariano Roque Alonso.</p>
                            </div>
                        </div>
                        <div class="timeline-card">
                            <img src="assets/images/historia/2019.png" alt="2019">
                            <div class="timeline-card-overlay">
                                <span class="timeline-year">2019</span>
                                <p>Apertura de la Sucursal Acceso Sur.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="timeline-nav timeline-next">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Testimonios Section -->
    <section class="testimonios-section">
        <div class="container">
            <div class="section-title">
                <h2>Testimoniales</h2>
            </div>
            
            <div class="testimonios-video-module">
                <div class="testimonio-video-main">
                    <iframe id="testimonioMainVideo" src="https://www.youtube.com/embed/oyxLuVOsxfk" title="Testimonio" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    <div class="testimonio-main-overlay">
                        <h4>LA BARCA DEL PESCADOR</h4>
                    </div>
                </div>
                
                <div class="testimonios-thumbs">
                    <div class="testimonio-thumb active" data-video="oyxLuVOsxfk" data-name="LA BARCA DEL PESCADOR" data-role="Propietario">
                        <img src="https://img.youtube.com/vi/oyxLuVOsxfk/mqdefault.jpg" alt="Astillero La Barca del Pescador">
                        <p>Astillero La Barca del Pescador</p>
                    </div>
                    <div class="testimonio-thumb" data-video="_rXWhwnsUT0" data-name="FERRETERÍA MARNI" data-role="Cliente">
                        <img src="https://img.youtube.com/vi/_rXWhwnsUT0/mqdefault.jpg" alt="Ferretería Marni">
                        <p>Ferretería Marni</p>
                    </div>
                    <div class="testimonio-thumb" data-video="hDDtHe-hgN4" data-name="METALÚRGICA VERA" data-role="Cliente">
                        <img src="https://img.youtube.com/vi/hDDtHe-hgN4/mqdefault.jpg" alt="Metalúrgica Vera">
                        <p>Metalúrgica Vera</p>
                    </div>
                    <div class="testimonio-thumb" data-video="SNqbOkwcgMM" data-name="NUEVO ESTILO S.A." data-role="Cliente">
                        <img src="https://img.youtube.com/vi/SNqbOkwcgMM/mqdefault.jpg" alt="Nuevo Estilo S.A.">
                        <p>Nuevo Estilo S.A.</p>
                    </div>
                    <div class="testimonio-thumb" data-video="H8BXrhKVNDg" data-name="RENTAMAQ" data-role="Cliente">
                        <img src="https://img.youtube.com/vi/H8BXrhKVNDg/mqdefault.jpg" alt="Rentamaq">
                        <p>Rentamaq</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Misión, Visión, Valores Section -->
    <section class="mvv-section">
        <div class="container">
            <div class="mvv-grid">
                <div class="mvv-item">
                    <h3>Nuestro Propósito</h3>
                    <p>Desarrollando nuestros activos intangibles, descomoditizamos nuestros servicios, nos focalizamos en la oportunidad, con resultados financieros para aumentar el valor de la Empresa.</p>
                </div>
                <div class="mvv-item">
                    <h3>Misión</h3>
                    <p>Somos una empresa dinámica, proveedores de productos para el mercado ferretero e industrial; que aporta soluciones efectivas a las necesidades de nuestros clientes a través de productos de calidad, asesoramiento y servicio técnico con personal altamente calificado.</p>
                </div>
                <div class="mvv-item">
                    <h3>Visión</h3>
                    <p>Ser la empresa líder en la provisión de productos y servicios para las ferreterías y las industrias, proporcionando una rentabilidad sostenida a sus accionistas, un ambiente de trabajo que permita el crecimiento profesional de sus empleados con el fin de producir un impacto social positivo en su entorno.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Valores Section -->
    <section class="valores-section">
        <div class="container">
            <div class="section-title">
                <h2>Valores</h2>
            </div>
            
            <div class="valores-grid">
                <div class="valor-item">
                    <div class="valor-icon">
                        <img src="assets/images/valores/integridad.png" alt="Integridad">
                    </div>
                    <h4>Integridad</h4>
                </div>
                <div class="valor-item">
                    <div class="valor-icon">
                        <img src="assets/images/valores/innovacion.png" alt="Innovación">
                    </div>
                    <h4>Innovación</h4>
                </div>
                <div class="valor-item">
                    <div class="valor-icon">
                        <img src="assets/images/valores/calidad.png" alt="Calidad">
                    </div>
                    <h4>Calidad</h4>
                </div>
                <div class="valor-item">
                    <div class="valor-icon">
                        <img src="assets/images/valores/tradicion.png" alt="Tradición">
                    </div>
                    <h4>Tradición</h4>
                </div>
            </div>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
