/* ============================================
   PETERSEN - JAVASCRIPT PRINCIPAL
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // MOBILE MENU
    // ============================================
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const body = document.body;

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            body.classList.toggle('menu-open');
            
            // Animate hamburger icon
            const spans = this.querySelectorAll('span');
            spans.forEach((span, index) => {
                span.classList.toggle('active');
            });
        });

        // Close menu when clicking a link (except submenu toggles)
        const mobileLinks = mobileMenu.querySelectorAll('a:not(.mobile-submenu-toggle)');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                body.classList.remove('menu-open');
            });
        });

        // Mobile submenu toggle
        const submenuToggles = mobileMenu.querySelectorAll('.mobile-submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.closest('.mobile-submenu');
                submenu.classList.toggle('active');
            });
        });
    }

    // ============================================
    // HEADER SCROLL EFFECT
    // ============================================
    const header = document.querySelector('.header');
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });

    // ============================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ============================================
    // CAROUSEL FUNCTIONALITY
    // ============================================
    class Carousel {
        constructor(container, options = {}) {
            this.container = container;
            this.track = container.querySelector('.carousel-track');
            this.slides = container.querySelectorAll('.carousel-slide');
            this.prevBtn = container.querySelector('.carousel-prev');
            this.nextBtn = container.querySelector('.carousel-next');
            this.dots = container.querySelectorAll('.carousel-dot');
            
            this.currentIndex = 0;
            this.slidesPerView = options.slidesPerView || 1;
            this.autoplay = options.autoplay || false;
            this.autoplaySpeed = options.autoplaySpeed || 5000;
            this.gap = options.gap || 30;
            
            this.init();
        }

        init() {
            if (this.slides.length === 0) return;
            
            this.updateSlideWidth();
            this.bindEvents();
            
            if (this.autoplay) {
                this.startAutoplay();
            }
            
            window.addEventListener('resize', () => this.updateSlideWidth());
        }

        updateSlideWidth() {
            const containerWidth = this.container.offsetWidth;
            const slideWidth = (containerWidth - (this.gap * (this.slidesPerView - 1))) / this.slidesPerView;
            
            this.slides.forEach(slide => {
                slide.style.minWidth = `${slideWidth}px`;
            });
        }

        bindEvents() {
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', () => this.prev());
            }
            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', () => this.next());
            }
            
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goTo(index));
            });

            // Touch events
            let startX, moveX;
            this.track.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            });
            
            this.track.addEventListener('touchmove', (e) => {
                moveX = e.touches[0].clientX;
            });
            
            this.track.addEventListener('touchend', () => {
                if (startX - moveX > 50) {
                    this.next();
                } else if (moveX - startX > 50) {
                    this.prev();
                }
            });
        }

        prev() {
            this.currentIndex = Math.max(0, this.currentIndex - 1);
            this.updatePosition();
        }

        next() {
            const maxIndex = this.slides.length - this.slidesPerView;
            this.currentIndex = Math.min(maxIndex, this.currentIndex + 1);
            this.updatePosition();
        }

        goTo(index) {
            this.currentIndex = index;
            this.updatePosition();
        }

        updatePosition() {
            const slideWidth = this.slides[0].offsetWidth + this.gap;
            const offset = -this.currentIndex * slideWidth;
            this.track.style.transform = `translateX(${offset}px)`;
            
            this.updateDots();
        }

        updateDots() {
            this.dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === this.currentIndex);
            });
        }

        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                if (this.currentIndex >= this.slides.length - this.slidesPerView) {
                    this.currentIndex = 0;
                } else {
                    this.currentIndex++;
                }
                this.updatePosition();
            }, this.autoplaySpeed);
        }

        stopAutoplay() {
            clearInterval(this.autoplayInterval);
        }
    }

    // Initialize carousels
    document.querySelectorAll('.carousel').forEach(carousel => {
        new Carousel(carousel, {
            slidesPerView: parseInt(carousel.dataset.slides) || 3,
            autoplay: carousel.dataset.autoplay === 'true',
            autoplaySpeed: parseInt(carousel.dataset.speed) || 5000,
            gap: parseInt(carousel.dataset.gap) || 30
        });
    });

    const rubrosCarousel = document.querySelector('.rubros-carousel');
    if (rubrosCarousel) {
        const rubrosTrack = rubrosCarousel.querySelector('.rubros-track');
        const prevBtn = rubrosCarousel.querySelector('.carousel-prev');
        const nextBtn = rubrosCarousel.querySelector('.carousel-next');

        const isMobileRubros = () => window.matchMedia('(max-width: 768px)').matches;

        const getScrollStep = () => {
            const card = rubrosTrack ? rubrosTrack.querySelector('.rubro-card') : null;
            if (!rubrosTrack || !card) return 0;
            const gap = parseFloat(getComputedStyle(rubrosTrack).gap) || 0;
            const cardWidth = card.getBoundingClientRect().width;
            return (cardWidth + gap) * 3;
        };

        if (rubrosTrack && prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!isMobileRubros()) return;
                const step = getScrollStep();
                rubrosTrack.scrollBy({ left: -step, behavior: 'smooth' });
            });
        }

        if (rubrosTrack && nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (!isMobileRubros()) return;
                const step = getScrollStep();
                rubrosTrack.scrollBy({ left: step, behavior: 'smooth' });
            });
        }
    }

    const aliadosCarousel = document.querySelector('.aliados-carousel');
    if (aliadosCarousel) {
        const aliadosTrack = aliadosCarousel.querySelector('.aliados-track');
        const prevBtn = aliadosCarousel.querySelector('.carousel-prev');
        const nextBtn = aliadosCarousel.querySelector('.carousel-next');

        const getScrollStep = () => {
            if (!aliadosTrack) return 0;
            return Math.max(aliadosTrack.clientWidth, 200);
        };

        if (aliadosTrack && prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const step = getScrollStep();
                aliadosTrack.scrollBy({ left: -step, behavior: 'smooth' });
            });
        }

        if (aliadosTrack && nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const step = getScrollStep();
                aliadosTrack.scrollBy({ left: step, behavior: 'smooth' });
            });
        }
    }

    // ============================================
    // TIMELINE CAROUSEL (Quienes Somos page)
    // ============================================
    const timelineCarousel = document.querySelector('.timeline-carousel');
    if (timelineCarousel) {
        const track = timelineCarousel.querySelector('.timeline-track');
        const prevBtn = timelineCarousel.querySelector('.timeline-prev');
        const nextBtn = timelineCarousel.querySelector('.timeline-next');
        const cards = timelineCarousel.querySelectorAll('.timeline-card');
        
        if (track && cards.length > 0) {
            let currentIndex = 0;
            
            function updateTimelineCarousel() {
                const cardWidth = cards[0].offsetWidth;
                const gap = 25;
                const offset = currentIndex * (cardWidth + gap);
                track.style.transform = `translateX(-${offset}px)`;
            }
            
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    currentIndex = Math.max(0, currentIndex - 1);
                    updateTimelineCarousel();
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    const visibleNow = window.innerWidth <= 576 ? 1 : (window.innerWidth <= 992 ? 2 : 3);
                    const maxNow = Math.max(0, cards.length - visibleNow);
                    currentIndex = Math.min(maxNow, currentIndex + 1);
                    updateTimelineCarousel();
                });
            }
            
            window.addEventListener('resize', () => {
                currentIndex = 0;
                updateTimelineCarousel();
            });
        }
    }

    // ============================================
    // SIMPLE SLIDER (for sucursales, timeline, etc.)
    // ============================================
    document.querySelectorAll('.slider-container').forEach(slider => {
        const track = slider.querySelector('.slider-track');
        const prevBtn = slider.querySelector('.slider-prev');
        const nextBtn = slider.querySelector('.slider-next');
        
        if (!track) return;
        
        let scrollAmount = 0;
        const scrollStep = 370;

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                scrollAmount = Math.max(0, scrollAmount - scrollStep);
                track.scrollTo({ left: scrollAmount, behavior: 'smooth' });
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                scrollAmount = Math.min(track.scrollWidth - track.clientWidth, scrollAmount + scrollStep);
                track.scrollTo({ left: scrollAmount, behavior: 'smooth' });
            });
        }
    });

    // ============================================
    // TESTIMONIOS VIDEO MODULE
    // ============================================
    const testimoniosVideoModule = document.querySelector('.testimonios-video-module');
    if (testimoniosVideoModule) {
        const mainIframe = testimoniosVideoModule.querySelector('#testimonioMainVideo');
        const mainName = testimoniosVideoModule.querySelector('.testimonio-main-overlay h4');
        const mainRole = testimoniosVideoModule.querySelector('.testimonio-main-overlay p');
        const thumbs = testimoniosVideoModule.querySelectorAll('.testimonio-thumb');

        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                thumbs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const videoId = this.dataset.video;
                const newName = this.dataset.name;
                const newRole = this.dataset.role;
                
                if (mainIframe && videoId) {
                    mainIframe.src = `https://www.youtube.com/embed/${videoId}`;
                }
                if (mainName) mainName.textContent = newName;
                if (mainRole) mainRole.textContent = newRole;
            });
        });
    }

    // ============================================
    // FORM VALIDATION
    // ============================================
    document.querySelectorAll('.contact-form form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });

            // Email validation
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    isValid = false;
                    emailField.classList.add('error');
                }
            }

            if (isValid) {
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'form-success';
                successMsg.innerHTML = '<p>¡Mensaje enviado correctamente! Nos pondremos en contacto pronto.</p>';
                form.appendChild(successMsg);
                
                // Reset form
                form.reset();
                
                // Remove success message after 5 seconds
                setTimeout(() => {
                    successMsg.remove();
                }, 5000);
            }
        });

        // Remove error class on input
        form.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });
    });

    // ============================================
    // SCROLL ANIMATIONS
    // ============================================
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // ============================================
    // VIDEO MODAL
    // ============================================
    const playButtons = document.querySelectorAll('.play-button');
    
    playButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const videoUrl = this.dataset.video;
            if (videoUrl) {
                openVideoModal(videoUrl);
            }
        });
    });

    function openVideoModal(url) {
        const modal = document.createElement('div');
        modal.className = 'video-modal';
        modal.innerHTML = `
            <div class="video-modal-content">
                <button class="video-modal-close">&times;</button>
                <iframe src="${url}" frameborder="0" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen title="Video"></iframe>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => modal.classList.add('active'), 10);
        
        modal.querySelector('.video-modal-close').addEventListener('click', () => {
            closeVideoModal(modal);
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeVideoModal(modal);
            }
        });
    }

    function closeVideoModal(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => modal.remove(), 300);
    }

    // ============================================
    // CATALOG DOWNLOAD MODAL (Recursos)
    // ============================================
    const catalogModal = document.getElementById('catalogModal');
    const catalogLeadForm = document.getElementById('catalogLeadForm');
    const catalogModalTitle = document.getElementById('catalogModalTitle');
    const catalogIdInput = document.getElementById('catalogId');
    const catalogPdfInput = document.getElementById('catalogPdf');

    function openCatalogModal(data) {
        if (!catalogModal) return;
        if (catalogModalTitle && data && data.title) {
            catalogModalTitle.textContent = data.title;
        }
        if (catalogIdInput) catalogIdInput.value = (data && data.id) ? data.id : '';
        if (catalogPdfInput) catalogPdfInput.value = (data && data.pdf) ? data.pdf : '';
        catalogModal.classList.add('active');
        catalogModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        const nameField = document.getElementById('catalogName');
        if (nameField) nameField.focus();
    }

    function closeCatalogModal() {
        if (!catalogModal) return;
        catalogModal.classList.remove('active');
        catalogModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.recurso-card').forEach(card => {
        card.addEventListener('click', (e) => {
            e.preventDefault();
            openCatalogModal({
                id: card.dataset.catalogId || '',
                title: card.dataset.catalogTitle || 'Descargar catálogo',
                pdf: card.dataset.catalogPdf || ''
            });
        });
    });

    if (catalogModal) {
        catalogModal.querySelectorAll('[data-catalog-modal-close]').forEach(btn => {
            btn.addEventListener('click', closeCatalogModal);
        });

        catalogModal.addEventListener('click', (e) => {
            if (e.target === catalogModal) {
                closeCatalogModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && catalogModal.classList.contains('active')) {
                closeCatalogModal();
            }
        });
    }

    if (catalogLeadForm) {
        catalogLeadForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // Placeholder: integración futura con CMS.
            // Aquí luego se enviarán los datos a un endpoint / CRM y se devolverá el PDF real.
            const payload = {
                catalog_id: catalogIdInput ? catalogIdInput.value : '',
                catalog_pdf: catalogPdfInput ? catalogPdfInput.value : '',
                name: document.getElementById('catalogName')?.value || '',
                phone: document.getElementById('catalogPhone')?.value || '',
                email: document.getElementById('catalogEmail')?.value || ''
            };

            console.log('[Recursos] Download requested (placeholder):', payload);
            closeCatalogModal();
            catalogLeadForm.reset();
        });
    }

    // ============================================
    // DROPDOWN MENU (for Rubros)
    // ============================================
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.nextElementSibling;
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.remove('active');
                }
            });
            
            dropdown.classList.toggle('active');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
                menu.classList.remove('active');
            });
        }
    });

    document.querySelectorAll('.dropdown-menu a').forEach(link => {
        link.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
                menu.classList.remove('active');
            });
        });
    });

    // ============================================
    // LAZY LOADING IMAGES
    // ============================================
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));

    // ============================================
    // WHATSAPP MODAL
    // ============================================
    const whatsappModal = document.getElementById('whatsapp-modal');
    const whatsappBtnDesktop = document.getElementById('whatsapp-btn-desktop');
    const whatsappBtnMobile = document.getElementById('whatsapp-btn-mobile');
    
    function openWhatsappModal() {
        if (whatsappModal) {
            whatsappModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            // Reset to main menu
            whatsappModal.querySelectorAll('.whatsapp-menu').forEach(menu => {
                menu.style.display = 'none';
            });
            const mainMenu = document.getElementById('whatsapp-menu-main');
            if (mainMenu) mainMenu.style.display = 'block';
        }
    }
    
    function closeWhatsappModal() {
        if (whatsappModal) {
            whatsappModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    // Open modal on button click
    if (whatsappBtnDesktop) {
        whatsappBtnDesktop.addEventListener('click', function(e) {
            e.preventDefault();
            openWhatsappModal();
        });
    }
    
    if (whatsappBtnMobile) {
        whatsappBtnMobile.addEventListener('click', function(e) {
            e.preventDefault();
            openWhatsappModal();
        });
    }
    
    if (whatsappModal) {
        // Close modal
        const closeBtn = whatsappModal.querySelector('.whatsapp-modal-close');
        const overlay = whatsappModal.querySelector('.whatsapp-modal-overlay');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closeWhatsappModal);
        }
        
        if (overlay) {
            overlay.addEventListener('click', closeWhatsappModal);
        }
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && whatsappModal.classList.contains('active')) {
                closeWhatsappModal();
            }
        });
        
        // Navigate between menus
        whatsappModal.querySelectorAll('.whatsapp-option[data-target]').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const targetMenu = document.getElementById(targetId);
                
                if (targetMenu) {
                    whatsappModal.querySelectorAll('.whatsapp-menu').forEach(menu => {
                        menu.style.display = 'none';
                    });
                    targetMenu.style.display = 'block';
                }
            });
        });
        
        // Back buttons
        whatsappModal.querySelectorAll('.whatsapp-back').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const targetMenu = document.getElementById(targetId);
                
                if (targetMenu) {
                    whatsappModal.querySelectorAll('.whatsapp-menu').forEach(menu => {
                        menu.style.display = 'none';
                    });
                    targetMenu.style.display = 'block';
                }
            });
        });
    }

    // ============================================
    // SEARCH FUNCTIONALITY
    // ============================================
    const searchForm = document.querySelector('.search-form');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rubro = this.querySelector('select[name="rubro"]')?.value;
            const marca = this.querySelector('select[name="marca"]')?.value;
            const busqueda = this.querySelector('input[name="busqueda"]')?.value;
            
            // Build search URL
            let searchUrl = 'tienda-online.html?';
            if (rubro) searchUrl += `rubro=${rubro}&`;
            if (marca) searchUrl += `marca=${marca}&`;
            if (busqueda) searchUrl += `q=${encodeURIComponent(busqueda)}`;
            
            window.location.href = searchUrl;
        });
    }

    // ============================================
    // COUNTER ANIMATION
    // ============================================
    const counters = document.querySelectorAll('.counter');
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.target);
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                
                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        counter.textContent = Math.floor(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                updateCounter();
                counterObserver.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => counterObserver.observe(counter));

    // ============================================
    // MAP INITIALIZATION (if using Google Maps)
    // ============================================
    window.initMap = function() {
        const mapContainers = document.querySelectorAll('.google-map');
        
        mapContainers.forEach(container => {
            const lat = parseFloat(container.dataset.lat) || -25.2867;
            const lng = parseFloat(container.dataset.lng) || -57.6470;
            
            const map = new google.maps.Map(container, {
                center: { lat, lng },
                zoom: 12,
                styles: [
                    {
                        featureType: 'all',
                        elementType: 'geometry.fill',
                        stylers: [{ saturation: -100 }]
                    }
                ]
            });
            
            // Add markers if data exists
            const markers = JSON.parse(container.dataset.markers || '[]');
            markers.forEach(markerData => {
                new google.maps.Marker({
                    position: { lat: markerData.lat, lng: markerData.lng },
                    map: map,
                    title: markerData.title
                });
            });
        });
    };

});

// ============================================
// VIDEO MODAL STYLES (injected)
// ============================================
const videoModalStyles = document.createElement('style');
videoModalStyles.textContent = `
    .video-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .video-modal.active {
        opacity: 1;
    }
    
    .video-modal-content {
        position: relative;
        width: 90%;
        max-width: 900px;
        aspect-ratio: 16/9;
    }
    
    .video-modal-content iframe {
        width: 100%;
        height: 100%;
    }
    
    .video-modal-close {
        position: absolute;
        top: -40px;
        right: 0;
        background: none;
        border: none;
        color: white;
        font-size: 36px;
        cursor: pointer;
    }
    
    .form-success {
        background: #4caf50;
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        text-align: center;
    }
    
    .form-group input.error,
    .form-group select.error,
    .form-group textarea.error {
        border-color: #f44336 !important;
        background-color: #fff5f5 !important;
    }
`;
document.head.appendChild(videoModalStyles);

// ============================================
// HISTORIA SLIDER
// ============================================
const historiaSlider = document.querySelector('.historia-slider');
if (historiaSlider) {
    const slides = historiaSlider.querySelectorAll('.slide');
    const dots = historiaSlider.querySelectorAll('.dot');
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            dots[i].classList.remove('active');
        });
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }

    // Auto-play
    function startAutoplay() {
        slideInterval = setInterval(nextSlide, 4000);
    }

    function stopAutoplay() {
        clearInterval(slideInterval);
    }

    // Dot click events
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            stopAutoplay();
            showSlide(index);
            startAutoplay();
        });
    });

    // Start autoplay
    startAutoplay();

    // Pause on hover
    historiaSlider.addEventListener('mouseenter', stopAutoplay);
    historiaSlider.addEventListener('mouseleave', startAutoplay);
}

// ============================================
// LIGHTBOX GALLERY
// ============================================
function initLightbox() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    if (galleryItems.length === 0) return;
    
    // Create lightbox HTML
    const lightboxHTML = `
        <div class="lightbox" id="lightbox">
            <div class="lightbox-content">
                <button class="lightbox-close" id="lightbox-close">&times;</button>
                <button class="lightbox-prev" id="lightbox-prev">‹</button>
                <button class="lightbox-next" id="lightbox-next">›</button>
                <img src="" alt="" id="lightbox-img">
                <div class="lightbox-counter" id="lightbox-counter"></div>
            </div>
        </div>
    `;
    
    // Add lightbox to body
    document.body.insertAdjacentHTML('beforeend', lightboxHTML);
    
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');
    const lightboxCounter = document.getElementById('lightbox-counter');
    
    let currentIndex = 0;
    const images = Array.from(galleryItems).map(item => ({
        src: item.querySelector('img').src,
        alt: item.querySelector('img').alt
    }));
    
    function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    function updateLightbox() {
        lightboxImg.src = images[currentIndex].src;
        lightboxImg.alt = images[currentIndex].alt;
        lightboxCounter.textContent = `${currentIndex + 1} / ${images.length}`;
    }
    
    function showNext() {
        currentIndex = (currentIndex + 1) % images.length;
        updateLightbox();
    }
    
    function showPrev() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateLightbox();
    }
    
    // Event listeners
    galleryItems.forEach((item, index) => {
        item.addEventListener('click', () => openLightbox(index));
    });
    
    lightboxClose.addEventListener('click', closeLightbox);
    lightboxNext.addEventListener('click', showNext);
    lightboxPrev.addEventListener('click', showPrev);
    
    // Close on background click
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('active')) return;
        
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') showNext();
        if (e.key === 'ArrowLeft') showPrev();
    });
}

// Initialize lightbox when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLightbox);
} else {
    initLightbox();
}
