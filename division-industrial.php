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
    <meta name="description" content="División Industrial de Petersen - Soluciones para vapor, bombeo, automatización y sistemas de aire comprimido.">
    <title>Industrial | Petersen</title>

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
    <div class="division-title-bar division-industrial">
        <div class="container">
            <div class="division-title-content">
                <img src="assets/images/icons/industrial.png" alt="Industrial" class="division-icon">
                <h1>Industria</h1>
            </div>
        </div>
    </div>

    <!-- Soluciones Section -->
    <section class="soluciones-section">
        <div class="container">
            <div class="section-head">
                <span class="section-eyebrow">División Industrial</span>
                <h2 class="soluciones-title">Soluciones</h2>
                <p class="section-sub">Cuatro campos de aplicación, respaldados por las marcas líderes del mundo.</p>
            </div>

            <div class="dominios-grid">
                <?php
                $dominios = [
                    [
                        'num'   => '01',
                        'nombre'=> 'Vapor',
                        'desc'  => 'Trampas, válvulas y control de vapor para máxima eficiencia térmica.',
                        'marca' => 'Spirax Sarco',
                        'img'   => 'industria2.jpg',
                        'alt'   => 'Válvula de control de vapor con posicionador',
                    ],
                    [
                        'num'   => '02',
                        'nombre'=> 'Bombeo',
                        'desc'  => 'Bombas centrífugas y sumergibles para procesos y servicios.',
                        'marca' => 'Grundfos',
                        'img'   => 'industria3.jpg',
                        'alt'   => 'Sala de bombas y tuberías bajo inspección técnica',
                    ],
                    [
                        'num'   => '03',
                        'nombre'=> 'Transmisión mecánica',
                        'desc'  => 'Reductores, acoples y componentes de potencia de larga vida.',
                        'marca' => 'Rexnord',
                        'img'   => 'industria1.jpg',
                        'alt'   => 'Sierra de cinta industrial en operación de corte',
                    ],
                    [
                        'num'   => '04',
                        'nombre'=> 'Corte y procesamiento',
                        'desc'  => 'Hojas y herramientas de corte para la industria frigorífica y de alimentos.',
                        'marca' => 'Starrett',
                        'img'   => 'industria4.jpg',
                        'alt'   => 'Corte de precisión con hoja Starrett en frigorífico',
                    ],
                ];
                foreach ($dominios as $d):
                ?>
                    <article class="dominio-card">
                        <div class="dominio-media">
                            <img src="assets/images/aplicaciones/<?php echo $d['img']; ?>" alt="<?php echo htmlspecialchars($d['alt']); ?>" loading="lazy">
                            <span class="dominio-num"><?php echo $d['num']; ?></span>
                        </div>
                        <div class="dominio-body">
                            <h3 class="dominio-nombre"><?php echo htmlspecialchars($d['nombre']); ?></h3>
                            <p class="dominio-desc"><?php echo htmlspecialchars($d['desc']); ?></p>
                            <span class="dominio-marca"><?php echo htmlspecialchars($d['marca']); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Marcas Carousel -->
    <section class="marcas-section marcas-carousel-section">
        <div class="container">
            <div class="section-head">
                <span class="section-eyebrow">Representaciones oficiales</span>
                <h2 class="marcas-title">Marcas</h2>
                <p class="section-sub">Importación directa de líderes mundiales en tecnología industrial.</p>
            </div>
        </div>
        <div class="marcas-marquee" aria-label="Carrusel de marcas">
            <div class="marcas-marquee-track">
                <?php
                $marcas_industrial = [
                    ['emax.png', 'EMAX'],
                    ['spiraxsarco.png', 'Spirax Sarco'],
                    ['grundfos.jpg', 'Grundfos'],
                    ['genebre.png', 'Genebre'],
                    ['sullair.jpg', 'Sullair'],
                    ['dunlop.jpg', 'Dunlop'],
                    ['klinger.jpg', 'Klinger'],
                    ['rexnord.jpg', 'Rexnord'],
                    ['danfoss.jpg', 'Danfoss'],
                    ['metalplan.jpg', 'Metalplan'],
                ];
                // Duplicado para el loop infinito
                foreach (array_merge($marcas_industrial, $marcas_industrial) as $marca):
                ?>
                    <div class="marca-logo">
                        <img src="assets/images/logos/<?php echo $marca[0]; ?>" alt="<?php echo htmlspecialchars($marca[1]); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contacto Section -->
    <section class="division-content division-contacto">
        <div class="container">
            <div class="division-grid">
                <div class="division-info">
                    <span class="section-eyebrow">Asesoría técnica</span>
                    <h2 class="contacto-heading">Hablemos de su planta.</h2>
                    <p>Importamos de forma directa las marcas líderes del mundo y acompañamos cada instalación con soporte técnico permanente: eficiencia energética, confiabilidad operativa y repuestos garantizados.</p>
                    <div class="dirigido">
                        <p><strong>Dirigido a:</strong> Industrias manufactureras, plantas de procesamiento, ingenios azucareros y empresas de servicios industriales.</p>
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
                            <a href="#" class="btn btn-whatsapp" data-phone="595981000000" data-message="Hola, me interesa información sobre la división Industrial.">
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
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
<script src="assets/js/forms.js"></script>
</body>
</html>
