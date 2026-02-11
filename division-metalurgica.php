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
    <meta name="description" content="División Metalúrgica de Petersen - Soluciones integrales para soldadura, corte y montaje industrial.">
    <title>Metalúrgica | Petersen</title>
    
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
    <div class="division-title-bar division-metalurgica">
        <div class="container">
            <div class="division-title-content">
                <img src="assets/images/icons/metalurgica.png" alt="Metalúrgica" class="division-icon">
                <h1>Metalúrgica</h1>
            </div>
        </div>
    </div>

    <!-- Division Hero -->
    <section class="division-hero" style="background-image: url('assets/images/hero-metalurgica.png');"></section>

    <!-- Division Content -->
    <section class="division-content">
        <div class="container">
            <div class="division-grid">
                <div class="division-info">
                    <p>En Petersen acompañamos a la industria metalúrgica paraguaya con soluciones integrales para soldadura, corte y montaje.</p>
                    <p>Proveemos equipos y accesorios de marcas líderes que garantizan precisión, potencia y durabilidad en cada proyecto, desde talleres livianos hasta montajes industriales de gran escala.</p>
                    <div class="dirigido">
                        <p><strong>Dirigido a:</strong> Metalúrgicas pesadas y livianas, astilleros, carroceros de buses y empresas de montaje industrial.</p>
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
                            <a href="#" class="btn btn-whatsapp" data-phone="595981000000" data-message="Hola, me interesa información sobre la división Metalúrgica.">
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
                    <img src="assets/images/aplicaciones/metalurgica1.jpg" alt="Equipos de soldadura">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica2.jpg" alt="Máquinas de corte">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica3.jpg" alt="Accesorios metalúrgicos">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica4.jpg" alt="Soldadura industrial">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica5.jpg" alt="Herramientas de corte">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica6.jpg" alt="Equipos profesionales">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica7.jpg" alt="Montaje industrial">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica8.jpg" alt="Soldadura especializada">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica9.jpg" alt="Equipamiento completo">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica10.jpg" alt="Herramientas de precisión">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica11.jpg" alt="Accesorios profesionales">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica12.jpg" alt="Equipos de montaje">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica13.jpg" alt="Soldadura avanzada">
                </div>
                <div class="gallery-item">
                    <img src="assets/images/aplicaciones/metalurgica14.jpg" alt="Soluciones integrales">
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
                    <img src="assets/images/logos/conarco.png" alt="Conarco">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/esab.png" alt="ESAB">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/okesab.jpg" alt="OK ESAB">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/sumig.png" alt="Sumig">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/thor.png" alt="Thor">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/neo.png" alt="Neo">
                </div>
                <div class="marca-logo">
                    <img src="assets/images/logos/starret.png" alt="Starrett">
                </div>
            </div>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
<script src="assets/js/forms.js"></script>
</body>
</html>
