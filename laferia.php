<?php
// Iniciar sesión y headers ANTES de cualquier output
require_once 'cms/includes/config.php';
require_once 'cms/includes/security.php';
require_once 'cms/includes/database.php';
require_once 'cms/includes/feria.php';
Security::setSecurityHeaders();

$db = Database::getInstance()->getConnection();
$feria = new Feria($db);
$config = $feria->getConfig();

// La fecha objetivo alimenta la cuenta regresiva del lado del cliente.
$fechaInicio = $config['fecha_inicio'];
$timestampInicio = $fechaInicio ? strtotime($fechaInicio) : 0;
$yaComenzo = $timestampInicio > 0 && $timestampInicio <= time();

// Rango de fechas legible: "12 al 15 de marzo de 2026"
$meses = [1=>'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$fechaLegible = '';
if ($timestampInicio > 0) {
    $tsFin = $config['fecha_fin'] ? strtotime($config['fecha_fin']) : 0;
    $dIni = (int)date('j', $timestampInicio);
    $mIni = (int)date('n', $timestampInicio);
    $aIni = date('Y', $timestampInicio);

    if ($tsFin > 0) {
        $dFin = (int)date('j', $tsFin);
        $mFin = (int)date('n', $tsFin);
        $fechaLegible = $mIni === $mFin
            ? "{$dIni} al {$dFin} de {$meses[$mIni]} de {$aIni}"
            : "{$dIni} de {$meses[$mIni]} al {$dFin} de {$meses[$mFin]} de {$aIni}";
    } else {
        $fechaLegible = "{$dIni} de {$meses[$mIni]} de {$aIni}";
    }
}

$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($config['subtitulo'] ?: 'La Feria Petersen: ofertas, demostraciones y las mejores marcas. Registrate y recibí el recordatorio.'); ?>">
    <title><?php echo htmlspecialchars($config['titulo']); ?> | Petersen</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>
<?php include 'includes/header.php'; ?>

    <!-- Hero de la Feria -->
    <section class="feria-hero<?php echo $config['banner'] ? '' : ' feria-hero-sin-banner'; ?>">
        <?php if ($config['banner']): ?>
            <img src="<?php echo htmlspecialchars($config['banner']); ?>"
                 alt="<?php echo htmlspecialchars($config['banner_alt']); ?>"
                 class="feria-hero-img">
        <?php endif; ?>
        <div class="feria-hero-overlay">
            <div class="container">
                <div class="feria-hero-content">
                    <span class="section-eyebrow">Petersen</span>
                    <h1 class="feria-hero-title"><?php echo htmlspecialchars($config['titulo']); ?></h1>
                    <?php if ($config['subtitulo']): ?>
                        <p class="feria-hero-sub"><?php echo htmlspecialchars($config['subtitulo']); ?></p>
                    <?php endif; ?>

                    <?php if ($fechaLegible || $config['lugar']): ?>
                        <div class="feria-meta">
                            <?php if ($fechaLegible): ?>
                                <span class="feria-meta-item">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                        <path d="M16 2v4M8 2v4M3 10h18"></path>
                                    </svg>
                                    <?php echo htmlspecialchars($fechaLegible); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($config['lugar']): ?>
                                <span class="feria-meta-item">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <?php echo htmlspecialchars($config['lugar']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <a href="#feria-registro" class="btn btn-primary feria-hero-cta">Quiero mi recordatorio</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Cuenta regresiva -->
    <?php if ($timestampInicio > 0): ?>
    <section class="feria-countdown-section">
        <div class="container">
            <div class="feria-countdown"
                 id="feriaCountdown"
                 data-target="<?php echo date('c', $timestampInicio); ?>"
                 data-mensaje-final="<?php echo htmlspecialchars($config['mensaje_final']); ?>">
                <p class="feria-countdown-label"><?php echo htmlspecialchars($config['countdown_texto']); ?></p>
                <div class="feria-countdown-grid" role="timer" aria-live="polite">
                    <div class="feria-countdown-unit">
                        <span class="feria-countdown-num" data-unit="dias">--</span>
                        <span class="feria-countdown-word">Días</span>
                    </div>
                    <div class="feria-countdown-unit">
                        <span class="feria-countdown-num" data-unit="horas">--</span>
                        <span class="feria-countdown-word">Horas</span>
                    </div>
                    <div class="feria-countdown-unit">
                        <span class="feria-countdown-num" data-unit="minutos">--</span>
                        <span class="feria-countdown-word">Minutos</span>
                    </div>
                    <div class="feria-countdown-unit">
                        <span class="feria-countdown-num" data-unit="segundos">--</span>
                        <span class="feria-countdown-word">Segundos</span>
                    </div>
                </div>
                <p class="feria-countdown-final"<?php echo $yaComenzo ? '' : ' hidden'; ?>>
                    <?php echo htmlspecialchars($config['mensaje_final']); ?>
                </p>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sobre la feria -->
    <?php if ($config['texto']): ?>
    <section class="feria-texto-section">
        <div class="container">
            <div class="section-head">
                <span class="section-eyebrow">Sobre el evento</span>
                <h2>¿Qué es <?php echo htmlspecialchars($config['titulo']); ?>?</h2>
            </div>
            <div class="feria-texto">
                <?php
                // El texto se guarda como texto plano desde el CMS; se respetan
                // los saltos de línea como párrafos.
                foreach (preg_split('/\R{2,}/', trim($config['texto'])) as $parrafo) {
                    if (trim($parrafo) === '') continue;
                    echo '<p>' . nl2br(htmlspecialchars(trim($parrafo))) . '</p>';
                }
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Formulario de recordatorio -->
    <section class="feria-registro-section" id="feria-registro">
        <div class="container">
            <div class="feria-registro-grid">
                <div class="feria-registro-info">
                    <span class="section-eyebrow">No te la pierdas</span>
                    <h2><?php echo htmlspecialchars($config['form_titulo']); ?></h2>
                    <p><?php echo htmlspecialchars($config['form_texto']); ?></p>
                </div>

                <div class="feria-form-card">
                    <form id="feriaForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                        <div class="form-message" role="alert" hidden></div>

                        <div class="form-group">
                            <label for="feria_nombre">Nombre completo <span aria-hidden="true">*</span></label>
                            <input type="text" id="feria_nombre" name="nombre" autocomplete="name" required>
                            <span class="field-error" data-error-for="nombre"></span>
                        </div>

                        <div class="form-group">
                            <label for="feria_telefono">Teléfono WhatsApp <span aria-hidden="true">*</span></label>
                            <input type="tel" id="feria_telefono" name="telefono" placeholder="0981 234 567" autocomplete="tel" required>
                            <span class="field-error" data-error-for="telefono"></span>
                        </div>

                        <div class="form-group">
                            <label for="feria_email">Email <span class="field-optional">(opcional)</span></label>
                            <input type="email" id="feria_email" name="email" autocomplete="email">
                            <span class="field-error" data-error-for="email"></span>
                        </div>

                        <div class="form-group">
                            <label for="feria_ciudad">Ciudad <span aria-hidden="true">*</span></label>
                            <input type="text" id="feria_ciudad" name="ciudad" autocomplete="address-level2" required>
                            <span class="field-error" data-error-for="ciudad"></span>
                        </div>

                        <!-- Honeypot anti-spam: oculto para personas -->
                        <div class="feria-hp" aria-hidden="true">
                            <label for="feria_website">No completar</label>
                            <input type="text" id="feria_website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <button type="submit" class="btn btn-primary feria-submit">Avisame de la feria</button>

                        <p class="feria-form-nota">Solo te escribiremos para recordarte el inicio de la feria.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
<script src="assets/js/feria.js"></script>
</body>
</html>
