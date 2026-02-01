<?php
// Iniciar sesión y headers ANTES de cualquier output
require_once 'cms/includes/config.php';
require_once 'cms/includes/security.php';
Security::setSecurityHeaders();

require_once 'cms/includes/database.php';
require_once 'cms/includes/catalog.php';

$db = Database::getInstance()->getConnection();
$catalogModel = new Catalog($db);
$catalogs = $catalogModel->getActive();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Recursos y catálogos de Petersen - Descarga nuestros catálogos técnicos y materiales informativos.">
    <title>Recursos | Petersen</title>
    
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
            <h1>Recursos</h1>
        </div>
    </div>

    <!-- Recursos Content -->
    <section class="recursos-content">
        <div class="container">
            <div class="recursos-intro">
                <h2>Catálogo Digital</h2>
                <p>¡Descargue nuestro catálogo digital, 4ta. edición! Elija el rubro de su preferencia.</p>
            </div>
            
            <div class="recursos-grid">
                <?php if (empty($catalogs)): ?>
                    <div class="empty-state">
                        <p>No hay catálogos disponibles en este momento.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($catalogs as $catalog): ?>
                        <a href="#" class="recurso-card" 
                           data-catalog-id="<?php echo $catalog['id']; ?>" 
                           data-catalog-title="<?php echo htmlspecialchars($catalog['title']); ?>" 
                           data-catalog-pdf="<?php echo htmlspecialchars($catalog['pdf_path']); ?>">
                            <?php if ($catalog['cover_image']): ?>
                                <img src="<?php echo htmlspecialchars($catalog['cover_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($catalog['title']); ?>">
                            <?php else: ?>
                                <div class="catalog-placeholder">
                                    <i class="fas fa-file-pdf"></i>
                                    <p><?php echo htmlspecialchars($catalog['title']); ?></p>
                                </div>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="catalog-modal" id="catalogModal" aria-hidden="true">
        <div class="catalog-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="catalogModalTitle">
            <button type="button" class="catalog-modal-close" data-catalog-modal-close aria-label="Cerrar">&times;</button>
            <div class="catalog-modal-header">
                <h3 id="catalogModalTitle" class="catalog-modal-title">Descargar catálogo</h3>
                <p class="catalog-modal-subtitle">Complete sus datos para descargar el PDF.</p>
            </div>

            <form class="catalog-modal-form" id="catalogLeadForm">
                <input type="hidden" name="catalog_id" id="catalogId" value="">
                <input type="hidden" name="catalog_pdf" id="catalogPdf" value="">

                <div class="catalog-form-row">
                    <label for="catalogName">Nombre</label>
                    <input id="catalogName" name="name" type="text" autocomplete="name" required>
                </div>

                <div class="catalog-form-row">
                    <label for="catalogPhone">Teléfono</label>
                    <input id="catalogPhone" name="phone" type="tel" autocomplete="tel" required>
                </div>

                <div class="catalog-form-row">
                    <label for="catalogEmail">Email</label>
                    <input id="catalogEmail" name="email" type="email" autocomplete="email" required>
                </div>

                <button type="submit" class="btn btn-primary catalog-modal-submit" id="catalogDownloadBtn">
                    Descargar PDF
                </button>
            </form>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
<script src="assets/js/forms.js"></script>
</body>
</html>
