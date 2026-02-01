<?php
// Iniciar sesión y headers ANTES de cualquier output
require_once 'cms/includes/config.php';
require_once 'cms/includes/security.php';
Security::setSecurityHeaders();

// Cargar posts desde la base de datos
require_once __DIR__ . '/cms/includes/database.php';
require_once __DIR__ . '/cms/includes/blog.php';

$blogModel = new Blog();
$posts = $blogModel->getAll('published');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Blog de Petersen - Noticias, capacitaciones y eventos de la empresa.">
    <title>Blog | Petersen</title>
    
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
            <h1>Blog</h1>
        </div>
    </div>

    <!-- Blog Content -->
    <section style="padding: 60px 0;">
        <div class="container">
            <?php if (empty($posts)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <h2 style="color: #2c3e5c; margin-bottom: 20px;">Próximamente</h2>
                    <p style="color: #666;">Estamos preparando contenido interesante para ti. ¡Vuelve pronto!</p>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach ($posts as $post): ?>
                    <article class="blog-card">
                        <a href="blog-post.php?slug=<?php echo urlencode($post['slug']); ?>">
                            <div class="blog-card-image">
                                <?php if ($post['featured_image']): ?>
                                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php else: ?>
                                    <img src="assets/images/blog/default.jpg" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="blog-card-content">
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <span class="blog-date">
                                    <?php 
                                    $date = new DateTime($post['published_at']);
                                    echo $date->format('d \d\e F, Y'); 
                                    ?>
                                </span>
                                <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            </div>
                        </a>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
