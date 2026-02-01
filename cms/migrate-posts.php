<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/blog.php';

// Este script migra los posts hardcodeados al CMS
// Solo debe ejecutarse una vez

$blogModel = new Blog();

// Post 1: Capacitación en Ferrizar
$post1 = [
    'title' => 'Capacitación en Ferrizar',
    'excerpt' => 'El pasado 15 de septiembre junto a nuestro cliente Ferrizar, realizamos una capacitación en espacios a su equipo de vendedores sobre equipos de soldar.',
    'content' => '<p>El pasado 15 de septiembre junto a nuestro cliente Ferrizar, realizamos una capacitación en espacios a su equipo de vendedores sobre equipos de soldar, acompañados de la utilización del Mod. de Experiencia Petersen.</p>

<p>Esta jornada de capacitación tuvo como objetivo principal fortalecer los conocimientos técnicos del equipo de ventas de Ferrizar, permitiéndoles ofrecer un mejor asesoramiento a sus clientes en la selección de equipos de soldadura.</p>

<p>Durante la capacitación, se abordaron temas como:</p>
<ul>
    <li>Tipos de equipos de soldadura y sus aplicaciones</li>
    <li>Características técnicas de los productos</li>
    <li>Ventajas competitivas de las marcas que representamos</li>
    <li>Demostración práctica con el Módulo de Experiencia Petersen</li>
</ul>

<p>Agradecemos a Ferrizar por la confianza depositada en Petersen y reafirmamos nuestro compromiso de seguir brindando capacitaciones de calidad a nuestros aliados comerciales.</p>',
    'featured_image' => 'assets/images/blog/blog1.png',
    'author_id' => 1, // Admin
    'status' => 'published'
];

// Post 2: Jornada de Capacitación Bristol
$post2 = [
    'title' => 'Jornada de Capacitación Bristol',
    'excerpt' => '¡Seguimos fortaleciendo alianzas! Realizamos una jornada de capacitación junto a la fuerza de ventas de Bristol en sus sucursales.',
    'content' => '<p>¡Seguimos fortaleciendo alianzas! Realizamos una jornada de capacitación junto a la fuerza de ventas de Bristol en sus sucursales de Caazapá, San Lorenzo, Ñemby, Acceso Sur, Villa Elisa y Areguá.</p>

<p>Esta iniciativa forma parte de nuestro programa de capacitación continua, diseñado para mantener actualizados a nuestros socios comerciales sobre las últimas novedades en productos y técnicas de venta.</p>

<p>Las sucursales visitadas fueron:</p>
<ul>
    <li>Caazapá</li>
    <li>San Lorenzo</li>
    <li>Ñemby</li>
    <li>Acceso Sur</li>
    <li>Villa Elisa</li>
    <li>Areguá</li>
</ul>

<p>En cada una de estas sucursales, nuestro equipo técnico compartió conocimientos sobre las características y beneficios de nuestros productos, así como estrategias de venta efectivas.</p>

<p>Agradecemos a todo el equipo de Bristol por su participación activa y entusiasmo durante las capacitaciones. ¡Juntos seguimos creciendo!</p>',
    'featured_image' => 'assets/images/blog/blog2.png',
    'author_id' => 1,
    'status' => 'published'
];

// Post 3: Demostración ESAB - Arco Sumergido
$post3 = [
    'title' => 'Demostración ESAB - Arco Sumergido',
    'excerpt' => 'Recibimos a Luis Chávez de ESAB, quien nos brindó una valiosa demostración de máquinas de soldar por Arco Sumergido.',
    'content' => '<p>El pasado 24 de junio estuvimos en nuestra Casa Matriz a Luis Chávez de ESAB, quien nos brindó una valiosa demostración de máquinas de soldar por Arco Sumergido dirigida especialmente a nuestra fuerza de ventas.</p>

<p>La soldadura por Arco Sumergido (SAW) es un proceso de soldadura por arco que utiliza un electrodo consumible alimentado de forma continua. El arco se mantiene sumergido bajo una capa de fundente granular, lo que proporciona una protección superior y una alta calidad de soldadura.</p>

<p>Durante la demostración, Luis Chávez compartió:</p>
<ul>
    <li>Principios fundamentales del proceso de Arco Sumergido</li>
    <li>Ventajas y aplicaciones industriales</li>
    <li>Configuración y operación de los equipos ESAB</li>
    <li>Mejores prácticas y consejos técnicos</li>
    <li>Demostración práctica en vivo</li>
</ul>

<p>Esta capacitación permite a nuestro equipo de ventas ofrecer un asesoramiento más completo y técnico a nuestros clientes que requieren soluciones de soldadura industrial de alto rendimiento.</p>

<p>Agradecemos a ESAB y a Luis Chávez por compartir su experiencia y conocimientos con el equipo Petersen.</p>',
    'featured_image' => 'assets/images/blog/blog3.png',
    'author_id' => 1,
    'status' => 'published'
];

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Migración de Posts - CMS Petersen</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
        h1 { color: #2c3e5c; }
        .info { color: #666; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Migración de Posts al CMS</h1>
    <p class='info'>Migrando 3 posts hardcodeados a la base de datos...</p>
";

// Migrar Post 1
echo "<h3>1. Capacitación en Ferrizar</h3>";
$result1 = $blogModel->create($post1);
if ($result1['success']) {
    echo "<div class='success'>✓ Post creado exitosamente (ID: {$result1['id']})</div>";
    
    // Actualizar fecha de publicación manualmente
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE blog_posts SET published_at = '2024-09-15 10:00:00', created_at = '2024-09-15 10:00:00' WHERE id = :id");
    $stmt->execute(['id' => $result1['id']]);
    echo "<div class='success'>✓ Fecha actualizada a: 15 de Septiembre, 2024</div>";
} else {
    echo "<div class='error'>✗ Error: {$result1['message']}</div>";
}

// Migrar Post 2
echo "<h3>2. Jornada de Capacitación Bristol</h3>";
$result2 = $blogModel->create($post2);
if ($result2['success']) {
    echo "<div class='success'>✓ Post creado exitosamente (ID: {$result2['id']})</div>";
    
    // Actualizar fecha de publicación manualmente
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE blog_posts SET published_at = '2024-08-10 10:00:00', created_at = '2024-08-10 10:00:00' WHERE id = :id");
    $stmt->execute(['id' => $result2['id']]);
    echo "<div class='success'>✓ Fecha actualizada a: 10 de Agosto, 2024</div>";
} else {
    echo "<div class='error'>✗ Error: {$result2['message']}</div>";
}

// Migrar Post 3
echo "<h3>3. Demostración ESAB - Arco Sumergido</h3>";
$result3 = $blogModel->create($post3);
if ($result3['success']) {
    echo "<div class='success'>✓ Post creado exitosamente (ID: {$result3['id']})</div>";
    
    // Actualizar fecha de publicación manualmente
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE blog_posts SET published_at = '2024-06-24 10:00:00', created_at = '2024-06-24 10:00:00' WHERE id = :id");
    $stmt->execute(['id' => $result3['id']]);
    echo "<div class='success'>✓ Fecha actualizada a: 24 de Junio, 2024</div>";
} else {
    echo "<div class='error'>✗ Error: {$result3['message']}</div>";
}

echo "
    <hr style='margin: 30px 0;'>
    <h2>Migración Completada</h2>
    <p class='info'>Los posts ahora están disponibles en el CMS y en la página pública del blog.</p>
    <p>
        <a href='blog.php' style='display: inline-block; padding: 10px 20px; background: #2c3e5c; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Ver Posts en CMS</a>
        <a href='../blog.php' style='display: inline-block; padding: 10px 20px; background: #f26522; color: white; text-decoration: none; border-radius: 5px;'>Ver Blog Público</a>
    </p>
    <p class='info'><strong>Nota:</strong> Puedes eliminar el archivo <code>migrate-posts.php</code> después de ejecutar esta migración.</p>
</body>
</html>";
