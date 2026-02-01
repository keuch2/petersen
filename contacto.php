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
    <meta name="description" content="Contacta con Petersen - Sede Corporativa, Logística y Central de Atención al Cliente.">
    <title>Contacto | Petersen</title>
    
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
            <h1>Contacto</h1>
        </div>
    </div>

    <!-- Contacto Content -->
    <section class="contacto-content">
        <div class="container">
            <div class="contacto-grid">
                <div class="contacto-info">
                    <div class="contacto-sede">
                        <h3>Sede Corporativa</h3>
                        <h4>Casa Central - Artigas (Asunción)</h4>
                        <p>Santo Tomás 1653 c/ Av. Artigas – Asunción</p>
                        <p>Tel: (021)206-131</p>
                        <p>Cel: +595 986 357950</p>
                    </div>
                    
                    <div class="contacto-sede">
                        <h3>Sede Logística</h3>
                        <h4>Casa Central - Artigas (Asunción)</h4>
                        <p>Santo Tomás 1653 c/ Av. Artigas – Asunción</p>
                        <p>Tel: (021)206-131</p>
                        <p>Cel: +595 986 357950</p>
                    </div>
                    
                    <div class="contacto-sede">
                        <h3>Central de Atención al Cliente</h3>
                        <h4>Casa Central - Artigas (Asunción)</h4>
                        <p>Santo Tomás 1653 c/ Av. Artigas – Asunción</p>
                        <p>Tel: (021)206-131</p>
                        <p>Cel: +595 986 357950</p>
                    </div>
                    
                    <div class="trabajo-cta">
                        <button type="button" class="btn btn-primary btn-trabajo" id="trabajeConNosotrosBtn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
                            </svg>
                            Trabaje con Nosotros
                        </button>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3>Contacta con Petersen</h3>
                    
                    <div id="formMessage" class="form-message" style="display: none;"></div>
                    
                    <form id="contactForm">
                        <div class="form-group">
                            <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo *" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" id="email" placeholder="Email Corporativo *" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" name="telefono" id="telefono" placeholder="Teléfono">
                        </div>
                        <div class="form-group">
                            <input type="text" name="empresa" id="empresa" placeholder="Empresa">
                        </div>
                        <div class="form-group">
                            <input type="text" name="ciudad" id="ciudad" placeholder="Ciudad">
                        </div>
                        <div class="form-group">
                            <select name="area" id="area" required>
                                <option value="">Seleccionar área *</option>
                                <option value="ventas">Ventas</option>
                                <option value="soporte">Soporte Técnico</option>
                                <option value="rrhh">Recursos Humanos</option>
                                <option value="administracion">Administración</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea name="mensaje" id="mensaje" placeholder="Mensaje *" rows="4" required></textarea>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="btn-text">Enviar por Email</span>
                                <span class="btn-loading" style="display: none;">Enviando...</span>
                            </button>
                            <button type="button" class="btn btn-whatsapp" id="whatsappBtn">
                                <i class="fab fa-whatsapp"></i>
                                <span class="btn-text">Contactar vía WhatsApp</span>
                                <span class="btn-loading" style="display: none;">Procesando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Trabaje con Nosotros -->
    <div class="job-modal" id="jobModal" aria-hidden="true">
        <div class="job-modal-overlay"></div>
        <div class="job-modal-content">
            <button type="button" class="job-modal-close" aria-label="Cerrar">&times;</button>
            
            <div class="job-modal-header">
                <h3 class="job-modal-title">Trabaje con Nosotros</h3>
                <p class="job-modal-subtitle">Complete el formulario y adjunte su CV</p>
            </div>

            <div id="jobFormMessage" class="form-message" style="display: none;"></div>

            <form class="job-modal-form" id="jobApplicationForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="jobName">Nombre Completo *</label>
                    <input type="text" id="jobName" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="jobPhone">Teléfono *</label>
                    <input type="tel" id="jobPhone" name="telefono" required>
                </div>

                <div class="form-group">
                    <label for="jobEmail">Email *</label>
                    <input type="email" id="jobEmail" name="email" required>
                </div>

                <div class="form-group">
                    <label for="jobPosition">Puesto al que se Postula *</label>
                    <input type="text" id="jobPosition" name="puesto" placeholder="Ej: Vendedor, Técnico, Administrativo" required>
                </div>

                <div class="form-group">
                    <label for="jobCV">Adjuntar CV *</label>
                    <input type="file" id="jobCV" name="cv" accept=".pdf,.doc,.docx" required>
                    <small class="form-text">Formatos permitidos: PDF, DOC, DOCX. Tamaño máximo: 5MB</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="jobSubmitBtn">
                        <span class="btn-text">Enviar Postulación</span>
                        <span class="btn-loading" style="display: none;">Enviando...</span>
                    </button>
                    <button type="button" class="btn btn-secondary" id="jobCancelBtn">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/contact-form.js"></script>
<script src="assets/js/job-application.js"></script>
</body>
</html>
