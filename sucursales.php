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
    <meta name="description" content="Sucursales de Petersen en Paraguay - Casa Central, Ciudad del Este, San Lorenzo y más.">
    <title>Sucursales | Petersen</title>
    
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
            <h1>Sucursales</h1>
        </div>
    </div>

    <!-- Map Section -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <section class="sucursales-map" style="margin-top: 0; border-radius: 0;">
        <div id="sucursalesMap" style="width: 100%; height: 450px;"></div>
    </section>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sucursales = [
            { name: "Casa Central", lat: -25.2867, lng: -57.6470, url: "https://maps.app.goo.gl/dP77fbZNrobLW9mL7" },
            { name: "Ciudad del Este", lat: -25.5097, lng: -54.6111, url: "https://maps.app.goo.gl/WgA5gcxoznocdwRo9" },
            { name: "Eusebio Ayala", lat: -25.2989, lng: -57.5856, url: "https://maps.app.goo.gl/UE1BNfNGgsv7o8tU9" },
            { name: "San Lorenzo", lat: -25.3389, lng: -57.5094, url: "https://maps.app.goo.gl/NZpY3F7pQ4NqVpFa9" },
            { name: "Mariano Roque Alonso", lat: -25.2019, lng: -57.5347, url: "https://maps.app.goo.gl/KLoYwSQk1uQwiETE8" },
            { name: "Acceso Sur", lat: -25.3564, lng: -57.5533, url: "https://maps.app.goo.gl/5RQ3FcTWawFaqrMj6" }
        ];
        
        const map = L.map('sucursalesMap').setView([-25.3, -56.5], 7);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        const bounds = [];
        
        const blueIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        sucursales.forEach(sucursal => {
            const marker = L.marker([sucursal.lat, sucursal.lng], { icon: blueIcon }).addTo(map);
            marker.bindPopup(`<strong>${sucursal.name}</strong><br><a href="${sucursal.url}" target="_blank" style="color: #EE7124;">Ver en Google Maps</a>`);
            bounds.push([sucursal.lat, sucursal.lng]);
        });
        
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    });
    </script>

    <!-- Sucursales List -->
    <section style="padding: 60px 0;">
        <div class="container">
            <div class="sucursales-list">
                <!-- Casa Central -->
                <div class="sucursal-item">
                    <div class="sucursal-item-image">
                        <img src="assets/images/sucursales/casacentral.jpg" alt="Casa Central">
                    </div>
                    <div class="sucursal-item-info">
                        <h3>Casa Central</h3>
                        <p>Santo Tomás 1653 c/ Av. Artigas – Asunción</p>
                        <p>Tel: (021)206-131</p>
                        <p>Cel: +595 986 357950</p>
                        <div class="sucursal-buttons">
                            <a href="#" class="btn btn-whatsapp" data-phone="595986357950">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                </svg>
                                WhatsApp
                            </a>
                            <a href="https://maps.app.goo.gl/dP77fbZNrobLW9mL7" target="_blank" class="btn btn-primary">
                                Ubicación Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ciudad del Este -->
                <div class="sucursal-item">
                    <div class="sucursal-item-image">
                        <img src="assets/images/sucursales/ciudaddeleste.jpg" alt="Ciudad del Este">
                    </div>
                    <div class="sucursal-item-info">
                        <h3>Ciudad del Este</h3>
                        <p>Avda. Itaipú Oeste c/ Monseñor Rodríguez</p>
                        <p>Ruta Km. 3 1/2 – Ciudad del Este</p>
                        <p>Tel: +595 981 413181</p>
                        <div class="sucursal-buttons">
                            <a href="#" class="btn btn-whatsapp" data-phone="595981413181">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                </svg>
                                WhatsApp
                            </a>
                            <a href="https://maps.app.goo.gl/WgA5gcxoznocdwRo9" target="_blank" class="btn btn-primary">
                                Ubicación Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Asunción - Eusebio Ayala -->
                <div class="sucursal-item">
                    <div class="sucursal-item-image">
                        <img src="assets/images/sucursales/eusebioayala.jpg" alt="Asunción - Eusebio Ayala">
                    </div>
                    <div class="sucursal-item-info">
                        <h3>Asunción - Avda. Eusebio Ayala</h3>
                        <p>Av. Eusebio Ayala 2799 – Asunción</p>
                        <p>Cel: +595 982 823089</p>
                        <div class="sucursal-buttons">
                            <a href="#" class="btn btn-whatsapp" data-phone="595982823089">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                </svg>
                                WhatsApp
                            </a>
                            <a href="https://maps.app.goo.gl/UE1BNfNGgsv7o8tU9" target="_blank" class="btn btn-primary">
                                Ubicación Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- San Lorenzo -->
                <div class="sucursal-item">
                    <div class="sucursal-item-image">
                        <img src="assets/images/sucursales/sanlorenzo.jpg" alt="San Lorenzo">
                    </div>
                    <div class="sucursal-item-info">
                        <h3>San Lorenzo</h3>
                        <p>Ruta II km 15 – San Lorenzo</p>
                        <p>Cel: +595 982 408122</p>
                        <div class="sucursal-buttons">
                            <a href="#" class="btn btn-whatsapp" data-phone="595982408122">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                </svg>
                                WhatsApp
                            </a>
                            <a href="https://maps.app.goo.gl/NZpY3F7pQ4NqVpFa9" target="_blank" class="btn btn-primary">
                                Ubicación Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mariano Roque Alonso -->
                <div class="sucursal-item">
                    <div class="sucursal-item-image">
                        <img src="assets/images/sucursales/marianoroquealonso.jpg" alt="Mariano Roque Alonso">
                    </div>
                    <div class="sucursal-item-info">
                        <h3>Mariano Roque Alonso</h3>
                        <p>Ruta Transchaco Km 16,5 c/ Roquerón</p>
                        <p>Mariano Roque Alonso</p>
                        <p>Tel: +595 993 579652</p>
                        <div class="sucursal-buttons">
                            <a href="#" class="btn btn-whatsapp" data-phone="595993579652">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                </svg>
                                WhatsApp
                            </a>
                            <a href="https://maps.app.goo.gl/KLoYwSQk1uQwiETE8" target="_blank" class="btn btn-primary">
                                Ubicación Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ñemby - Acceso Sur -->
                <div class="sucursal-item">
                    <div class="sucursal-item-image">
                        <img src="assets/images/sucursales/accesosur.jpg" alt="Ñemby - Acceso Sur">
                    </div>
                    <div class="sucursal-item-info">
                        <h3>Ñemby - Acceso Sur</h3>
                        <p>Divino Niño Jesús casi Acceso Sur – Ñemby</p>
                        <p>Tel: +595 986 709879</p>
                        <div class="sucursal-buttons">
                            <a href="#" class="btn btn-whatsapp" data-phone="595986709879">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                </svg>
                                WhatsApp
                            </a>
                            <a href="https://maps.app.goo.gl/5RQ3FcTWawFaqrMj6" target="_blank" class="btn btn-primary">
                                Ubicación Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
