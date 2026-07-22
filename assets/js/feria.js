// La Feria Petersen: cuenta regresiva y formulario de recordatorio
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // CUENTA REGRESIVA
    // ==========================================
    const countdown = document.getElementById('feriaCountdown');

    if (countdown) {
        const target = new Date(countdown.dataset.target).getTime();
        const grid = countdown.querySelector('.feria-countdown-grid');
        const label = countdown.querySelector('.feria-countdown-label');
        const finalMsg = countdown.querySelector('.feria-countdown-final');
        const units = {
            dias: countdown.querySelector('[data-unit="dias"]'),
            horas: countdown.querySelector('[data-unit="horas"]'),
            minutos: countdown.querySelector('[data-unit="minutos"]'),
            segundos: countdown.querySelector('[data-unit="segundos"]')
        };

        const pad = (n) => String(n).padStart(2, '0');

        function render() {
            const diff = target - Date.now();

            if (diff <= 0) {
                // La feria ya comenzó: mostrar el mensaje final.
                if (grid) grid.hidden = true;
                if (label) label.hidden = true;
                if (finalMsg) finalMsg.hidden = false;
                clearInterval(timer);
                return;
            }

            const totalSeconds = Math.floor(diff / 1000);
            units.dias.textContent = pad(Math.floor(totalSeconds / 86400));
            units.horas.textContent = pad(Math.floor((totalSeconds % 86400) / 3600));
            units.minutos.textContent = pad(Math.floor((totalSeconds % 3600) / 60));
            units.segundos.textContent = pad(totalSeconds % 60);
        }

        render();
        const timer = setInterval(render, 1000);
    }

    // ==========================================
    // FORMULARIO DE RECORDATORIO
    // ==========================================
    const form = document.getElementById('feriaForm');
    if (!form) return;

    const messageEl = form.querySelector('.form-message');
    const submitBtn = form.querySelector('button[type="submit"]');

    function showMessage(type, text) {
        messageEl.className = 'form-message ' + type;
        messageEl.textContent = text;
        messageEl.hidden = false;
    }

    function clearErrors() {
        form.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; });
        form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
        messageEl.hidden = true;
    }

    function showFieldErrors(errors) {
        Object.keys(errors).forEach(field => {
            const errorEl = form.querySelector('[data-error-for="' + field + '"]');
            const input = form.querySelector('[name="' + field + '"]');
            if (errorEl) errorEl.textContent = errors[field];
            if (input) input.classList.add('has-error');
        });

        const firstInvalid = form.querySelector('.has-error');
        if (firstInvalid) firstInvalid.focus();
    }

    // Validación en el cliente; el servidor vuelve a validar siempre.
    function validate() {
        const errors = {};
        const nombre = form.nombre.value.trim();
        const telefono = form.telefono.value.trim();
        const email = form.email.value.trim();
        const ciudad = form.ciudad.value.trim();

        if (nombre.length < 3) {
            errors.nombre = 'Ingresá tu nombre completo';
        }

        // Entre 9 y 13 dígitos cubre 0981234567 y 595981234567.
        const digits = telefono.replace(/\D/g, '');
        if (digits.length < 9 || digits.length > 13) {
            errors.telefono = 'Ingresá un número de WhatsApp válido (ej: 0981 234 567)';
        }

        if (email !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.email = 'El email no es válido';
        }

        if (ciudad === '') {
            errors.ciudad = 'Ingresá tu ciudad';
        }

        return errors;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        const errors = validate();
        if (Object.keys(errors).length > 0) {
            showFieldErrors(errors);
            return;
        }

        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Enviando...';

        try {
            const response = await fetch('includes/feria-handler.php', {
                method: 'POST',
                body: new FormData(form)
            });

            const data = await response.json();

            if (data.success) {
                form.reset();
                showMessage('success', data.message);
            } else {
                showMessage('error', data.message || 'No pudimos guardar tu registro.');
                if (data.errors) showFieldErrors(data.errors);
            }
        } catch (error) {
            showMessage('error', 'Error de conexión. Verificá tu internet e intentá de nuevo.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    // Limpiar el error de un campo apenas el visitante lo corrige.
    form.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function () {
            if (!input.classList.contains('has-error')) return;
            input.classList.remove('has-error');
            const errorEl = form.querySelector('[data-error-for="' + input.name + '"]');
            if (errorEl) errorEl.textContent = '';
        });
    });
});
