// Job Application Modal Handler
(function() {
    'use strict';
    
    const modal = document.getElementById('jobModal');
    const openBtn = document.getElementById('trabajeConNosotrosBtn');
    const closeBtn = modal?.querySelector('.job-modal-close');
    const cancelBtn = document.getElementById('jobCancelBtn');
    const overlay = modal?.querySelector('.job-modal-overlay');
    const form = document.getElementById('jobApplicationForm');
    const submitBtn = document.getElementById('jobSubmitBtn');
    const messageDiv = document.getElementById('jobFormMessage');
    
    if (!modal || !openBtn || !form) return;
    
    // Abrir modal
    function openModal() {
        modal.setAttribute('aria-hidden', 'false');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Focus en primer campo
        setTimeout(() => {
            document.getElementById('jobName')?.focus();
        }, 100);
    }
    
    // Cerrar modal
    function closeModal() {
        modal.setAttribute('aria-hidden', 'true');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        form.reset();
        hideMessage();
    }
    
    // Mostrar mensaje
    function showMessage(message, type) {
        messageDiv.textContent = message;
        messageDiv.className = 'form-message ' + (type === 'success' ? 'success' : 'error');
        messageDiv.style.display = 'block';
        
        // Scroll al mensaje
        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    // Ocultar mensaje
    function hideMessage() {
        messageDiv.style.display = 'none';
    }
    
    // Validar archivo
    function validateFile(file) {
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type)) {
            return 'Formato de archivo no permitido. Use PDF, DOC o DOCX';
        }
        
        if (file.size > maxSize) {
            return 'El archivo es demasiado grande. Máximo 5MB';
        }
        
        return null;
    }
    
    // Event listeners
    openBtn.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
            closeModal();
        }
    });
    
    // Validar archivo al seleccionar
    const fileInput = document.getElementById('jobCV');
    fileInput?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const error = validateFile(file);
            if (error) {
                showMessage(error, 'error');
                e.target.value = '';
            } else {
                hideMessage();
            }
        }
    });
    
    // Submit form
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        hideMessage();
        
        // Validar archivo
        const fileInput = document.getElementById('jobCV');
        const file = fileInput.files[0];
        
        if (!file) {
            showMessage('Debe adjuntar su CV', 'error');
            return;
        }
        
        const fileError = validateFile(file);
        if (fileError) {
            showMessage(fileError, 'error');
            return;
        }
        
        // Deshabilitar botón
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch('includes/job-application-handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage(data.message, 'success');
                
                // Cerrar modal después de 3 segundos
                setTimeout(() => {
                    closeModal();
                }, 3000);
            } else {
                showMessage(data.message || 'Error al enviar la postulación', 'error');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showMessage('Error al procesar la solicitud. Por favor, intente nuevamente.', 'error');
        } finally {
            // Rehabilitar botón
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        }
    });
    
})();
