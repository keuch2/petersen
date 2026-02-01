// Sistema unificado de formularios para Petersen
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // FORMULARIOS DE DIVISIONES Y SERVICIOS
    // ==========================================
    const divisionForms = document.querySelectorAll('.contact-form form:not(#contactForm):not(#catalogLeadForm)');
    
    divisionForms.forEach(form => {
        // Agregar IDs y elementos necesarios
        if (!form.id) {
            form.id = 'divisionForm_' + Math.random().toString(36).substr(2, 9);
        }
        
        // Agregar campo hidden para tipo de formulario
        const formType = determineFormType(form);
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'form_type';
        hiddenInput.value = formType;
        form.appendChild(hiddenInput);
        
        // Agregar mensaje de respuesta
        const messageDiv = document.createElement('div');
        messageDiv.className = 'form-message';
        messageDiv.style.display = 'none';
        form.insertBefore(messageDiv, form.firstChild);
        
        // Manejar envío por email
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const whatsappBtn = form.querySelector('.btn-whatsapp');
            const messageEl = form.querySelector('.form-message');
            
            if (!validateDivisionForm(form, messageEl)) {
                return;
            }
            
            // Deshabilitar botones
            submitBtn.disabled = true;
            if (whatsappBtn) whatsappBtn.style.pointerEvents = 'none';
            
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Enviando...';
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch('includes/form-handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showFormMessage(messageEl, 'success', data.message);
                    form.reset();
                } else {
                    showFormMessage(messageEl, 'error', data.message || 'Error al enviar el mensaje');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showFormMessage(messageEl, 'error', 'Error al procesar la solicitud');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                if (whatsappBtn) whatsappBtn.style.pointerEvents = 'auto';
            }
        });
        
        // Manejar botón de WhatsApp
        const whatsappBtn = form.querySelector('.btn-whatsapp');
        if (whatsappBtn) {
            whatsappBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                
                const messageEl = form.querySelector('.form-message');
                
                if (!validateDivisionForm(form, messageEl)) {
                    return;
                }
                
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                whatsappBtn.style.pointerEvents = 'none';
                
                try {
                    const formData = new FormData(form);
                    
                    const response = await fetch('includes/whatsapp-handler.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success && data.whatsapp_url) {
                        showFormMessage(messageEl, 'success', 'Datos guardados. Redirigiendo a WhatsApp...');
                        
                        setTimeout(() => {
                            window.open(data.whatsapp_url, '_blank');
                            form.reset();
                            messageEl.style.display = 'none';
                        }, 1000);
                    } else {
                        showFormMessage(messageEl, 'error', data.message || 'Error al procesar la solicitud');
                    }
                    
                } catch (error) {
                    console.error('Error:', error);
                    showFormMessage(messageEl, 'error', 'Error al procesar la solicitud');
                } finally {
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        whatsappBtn.style.pointerEvents = 'auto';
                    }, 1500);
                }
            });
        }
    });
    
    // ==========================================
    // FORMULARIO DE CATÁLOGOS
    // ==========================================
    const catalogForm = document.getElementById('catalogLeadForm');
    if (catalogForm) {
        catalogForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('catalogDownloadBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Procesando...';
            
            try {
                const formData = new FormData(catalogForm);
                
                // Guardar lead y obtener URL de descarga
                const response = await fetch('includes/catalog-download-handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Descargar PDF
                    if (data.download_url) {
                        window.open(data.download_url, '_blank');
                    }
                    
                    // Cerrar modal
                    const modal = document.getElementById('catalogModal');
                    if (modal) {
                        modal.setAttribute('aria-hidden', 'true');
                        modal.style.display = 'none';
                    }
                    
                    catalogForm.reset();
                    
                    // Mostrar mensaje de éxito
                    alert('¡Gracias! Tu descarga comenzará en breve.');
                } else {
                    alert(data.message || 'Error al procesar la solicitud');
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
    
    // ==========================================
    // FUNCIONES AUXILIARES
    // ==========================================
    function determineFormType(form) {
        const url = window.location.pathname;
        
        if (url.includes('bosque-y-jardin')) return 'division_bosque';
        if (url.includes('construccion')) return 'division_construccion';
        if (url.includes('ferreteria')) return 'division_ferreteria';
        if (url.includes('industrial')) return 'division_industrial';
        if (url.includes('mecanica')) return 'division_mecanica';
        if (url.includes('metalurgica')) return 'division_metalurgica';
        if (url.includes('servicios')) return 'servicios';
        
        return 'general';
    }
    
    function validateDivisionForm(form, messageEl) {
        const nombre = form.querySelector('[name="nombre"]').value.trim();
        const email = form.querySelector('[name="email"]').value.trim();
        
        if (!nombre || !email) {
            showFormMessage(messageEl, 'error', 'Por favor, completa los campos de nombre y email');
            return false;
        }
        
        if (!isValidEmail(email)) {
            showFormMessage(messageEl, 'error', 'Por favor, ingresa un email válido');
            return false;
        }
        
        return true;
    }
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function showFormMessage(element, type, message) {
        element.className = 'form-message ' + type;
        element.textContent = message;
        element.style.display = 'block';
        
        element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        if (type === 'success') {
            setTimeout(() => {
                element.style.display = 'none';
            }, 5000);
        }
    }
});
