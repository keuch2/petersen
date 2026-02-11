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
    <meta name="description" content="División Bosque & Jardín de Petersen - Equipos forestales, motosierras y herramientas de mantenimiento.">
    <title>Bosque & Jardín | Petersen</title>
    
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
            <h1>Rubros</h1>
        </div>
    </div>

    <!-- Division Title Bar -->
    <div class="division-title-bar division-bosque">
        <div class="container">
            <div class="division-title-content">
                <img src="assets/images/icons/forestal.png" alt="Bosque & Jardín" class="division-icon">
                <h1>Bosque & Jardín</h1>
            </div>
        </div>
    </div>

    <!-- Division Hero -->
    <section class="division-hero" style="background-image: url('assets/images/hero-bosqueyjardin.png');"></section>

    <!-- Division Content -->
    <section class="division-content">
        <div class="container">
            <div class="division-grid">
                <div class="division-info">
                    <p>En Petersen llevamos la fuerza de las marcas líderes al campo y al jardín.</p>
                    <p>Ofrecemos equipos forestales, motosierras y herramientas de mantenimiento diseñadas para el trabajo profesional y doméstico.</p>
                    <div class="dirigido">
                        <p><strong>Dirigido a:</strong> Empresas forestales, paisajistas, municipalidades y propietarios de fincas.</p>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3>Solicitar Asesoría</h3>
                    <form>
                        <div class="form-group">
                            <input type="text" name="nombre" placeholder="Nombre Completo" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email Corporativo" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" name="telefono" placeholder="Teléfono">
                        </div>
                        <div class="form-group">
                            <input type="text" name="empresa" placeholder="Empresa">
                        </div>
                        <div class="form-group">
                            <input type="text" name="ciudad" placeholder="Ciudad">
                        </div>
                        <div class="form-buttons">
                            <button type="submit" class="btn btn-primary">Enviar</button>
                            <a href="#" class="btn btn-whatsapp" data-phone="595981000000" data-message="Hola, me interesa información sobre la división Bosque & Jardín.">
                                Solicitar vía WhatsApp
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                </svg>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Soluciones Section -->
    <section class="soluciones-section">
        <div class="container">
            <h2 class="soluciones-title">Soluciones</h2>
            
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/bosqueyjardin1.jpg" alt="Equipos forestales">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/bosqueyjardin2.jpg" alt="Motosierras profesionales">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/bosqueyjardin3.jpg" alt="Herramientas de jardín">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/bosqueyjardin4.jpg" alt="Equipos de corte">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/bosqueyjardin5.jpg" alt="Desmalezadoras">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/bosqueyjardin6.jpg" alt="Sopladoras">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/bosqueyjardin7.jpg" alt="Accesorios forestales">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/bosqueyjardin8.jpg" alt="Equipamiento completo">
                </div>
            </div>
        </div>
    </section>

    <!-- Marcas Section -->
    <section class="marcas-section">
        <div class="container">
            <h2 class="marcas-title">Marcas</h2>
            
            <div class="marcas-grid">
                <div class="marca-logo">
                    <img src="assets/images/logos/truper.png" alt="Truper">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/forestgarden.png" alt="Forest & Garden">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/gladiator.png" alt="Gladiator">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/oleomac.png" alt="Oleo-Mac">
                </div>
            </div>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
<script src="assets/js/forms.js"></script>
</body>
</html>
