// Manejo del formulario de contacto
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const whatsappBtn = document.getElementById('whatsappBtn');
    const formMessage = document.getElementById('formMessage');
    
    // Envío por email
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validar formulario
        if (!validateForm()) {
            return;
        }
        
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');
        
        // Deshabilitar botones y mostrar loading
        submitBtn.disabled = true;
        whatsappBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        
        // Ocultar mensajes anteriores
        formMessage.style.display = 'none';
        
        try {
            // Preparar datos
            const formData = new FormData(form);
            
            // Enviar formulario
            const response = await fetch('includes/contact-handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage('success', data.message);
                form.reset();
            } else {
                showMessage('error', data.message || 'Error al enviar el mensaje');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showMessage('error', 'Error al procesar la solicitud. Por favor, intenta nuevamente.');
        } finally {
            // Rehabilitar botones
            submitBtn.disabled = false;
            whatsappBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        }
    });
    
    // Envío por WhatsApp
    whatsappBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        // Validar formulario
        if (!validateForm()) {
            return;
        }
        
        const btnText = whatsappBtn.querySelector('.btn-text');
        const btnLoading = whatsappBtn.querySelector('.btn-loading');
        
        // Deshabilitar botones y mostrar loading
        submitBtn.disabled = true;
        whatsappBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        
        // Ocultar mensajes anteriores
        formMessage.style.display = 'none';
        
        try {
            // Preparar datos
            const formData = new FormData(form);
            
            // Enviar a handler de WhatsApp
            const response = await fetch('includes/whatsapp-handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success && data.whatsapp_url) {
                // Mostrar mensaje de éxito
                showMessage('success', 'Datos guardados. Redirigiendo a WhatsApp...');
                
                // Esperar 1 segundo y abrir WhatsApp
                setTimeout(() => {
                    window.open(data.whatsapp_url, '_blank');
                    form.reset();
                    formMessage.style.display = 'none';
                }, 1000);
            } else {
                showMessage('error', data.message || 'Error al procesar la solicitud');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showMessage('error', 'Error al procesar la solicitud. Por favor, intenta nuevamente.');
        } finally {
            // Rehabilitar botones después de un delay
            setTimeout(() => {
                submitBtn.disabled = false;
                whatsappBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }, 1500);
        }
    });
    
    function validateForm() {
        const nombre = document.getElementById('nombre').value.trim();
        const email = document.getElementById('email').value.trim();
        const area = document.getElementById('area').value;
        const mensaje = document.getElementById('mensaje').value.trim();
        
        if (!nombre || !email || !area || !mensaje) {
            showMessage('error', 'Por favor, completa todos los campos obligatorios (*)');
            return false;
        }
        
        if (!isValidEmail(email)) {
            showMessage('error', 'Por favor, ingresa un email válido');
            return false;
        }
        
        if (mensaje.length < 10) {
            showMessage('error', 'El mensaje debe tener al menos 10 caracteres');
            return false;
        }
        
        return true;
    }
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function showMessage(type, message) {
        formMessage.className = 'form-message ' + type;
        formMessage.textContent = message;
        formMessage.style.display = 'block';
        
        // Scroll al mensaje
        formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // Auto-ocultar después de 5 segundos si es éxito
        if (type === 'success') {
            setTimeout(() => {
                formMessage.style.display = 'none';
            }, 5000);
        }
    }
});
