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
    <meta name="description" content="Servicios de Petersen - Asesoría técnica, soporte postventa, capacitación y atención especializada.">
    <title>Servicios | Petersen</title>
    
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
            <h1>Servicios</h1>
        </div>
    </div>

    <!-- Hero Image -->
    <div class="servicios-hero" style="background-image: url('assets/images/servicios-hero.png');"></div>

    <!-- Servicios Content -->
    <section class="servicios-content">
        <div class="container">
            <div class="servicios-grid">
                <div class="servicios-info">
                    <h2>En Petersen acompañamos cada etapa de tu compra con un servicio integral que garantiza resultados duraderos.</h2>
                    <p>Nuestro compromiso no termina con la venta: brindamos asesoría técnica, soporte postventa y capacitación constante para asegurar el máximo rendimiento de cada producto.</p>
                    
                    <div class="servicio-item">
                        <h4>Asesoría técnica</h4>
                        <p>Orientación profesional para elegir las soluciones más adecuadas según cada necesidad.</p>
                    </div>
                    
                    <div class="servicio-item">
                        <h4>Soporte postventa</h4>
                        <p>Atención continua, repuestos y mantenimiento para prolongar la vida útil de tus equipos.</p>
                    </div>
                    
                    <div class="servicio-item">
                        <h4>Capacitación</h4>
                        <p>Entrenamientos y demostraciones para optimizar el uso y cuidado de las herramientas.</p>
                    </div>
                    
                    <div class="servicio-item">
                        <h4>Atención especializada</h4>
                        <p>Equipo humano con experiencia en cada división para ofrecer respuestas rápidas y efectivas.</p>
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
                        <div class="form-group">
                            <select name="area" required>
                                <option value="">Seleccionar área</option>
                                <option value="forestal">Bosque & Jardín</option>
                                <option value="industrial">Industrial</option>
                                <option value="construccion">Construcción</option>
                                <option value="metalurgica">Metalúrgica</option>
                                <option value="mecanica">Mecánica</option>
                                <option value="ferreteria">Ferretería</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea name="mensaje" placeholder="Mensaje" rows="4"></textarea>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" class="btn btn-primary">Enviar</button>
                            <a href="#" class="btn btn-whatsapp" data-phone="595981000000" data-message="Hola, me gustaría solicitar asesoría sobre sus servicios.">
                                Solicitar vía WhatsApp
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
<script src="assets/js/forms.js"></script>
</body>
</html>
