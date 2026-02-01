<?php
// Iniciar sesión y headers ANTES de cualquier output
require_once 'cms/includes/config.php';
require_once 'cms/includes/security.php';
Security::setSecurityHeaders();

// Cargar post individual desde la base de datos
require_once __DIR__ . '/cms/includes/database.php';
require_once __DIR__ . '/cms/includes/blog.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: blog.php');
    exit;
}

$blogModel = new Blog();
$post = $blogModel->getBySlug($slug);

if (!$post || $post['status'] !== 'published') {
    header('Location: blog.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 160)); ?>">
    <title><?php echo htmlspecialchars($post['title']); ?> | Blog Petersen</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <style>
        .blog-article {
            padding: 60px 0;
        }
        
        .blog-article-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .blog-back {
            color: #2c3e5c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .blog-back:hover {
            color: #f26522;
        }
        
        .blog-date {
            color: #999;
            font-size: 14px;
        }
        
        .blog-article-title {
            font-size: 36px;
            font-weight: 700;
            color: #2c3e5c;
            margin-bottom: 30px;
            line-height: 1.3;
        }
        
        .blog-article-image {
            margin-bottom: 40px;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .blog-article-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .blog-article-content {
            font-size: 16px;
            line-height: 1.8;
            color: #333;
        }
        
        .blog-article-content p {
            margin-bottom: 20px;
        }
        
        .blog-article-content h2 {
            font-size: 28px;
            margin: 40px 0 20px;
            color: #2c3e5c;
        }
        
        .blog-article-content h3 {
            font-size: 22px;
            margin: 30px 0 15px;
            color: #2c3e5c;
        }
        
        .blog-article-content ul,
        .blog-article-content ol {
            margin: 20px 0;
            padding-left: 30px;
        }
        
        .blog-article-content li {
            margin-bottom: 10px;
        }
        
        .blog-article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 30px 0;
        }
        
        .blog-article-content iframe {
            max-width: 100%;
            margin: 30px 0;
            border-radius: 10px;
        }
        
        .blog-article-content blockquote {
            border-left: 4px solid #f26522;
            padding-left: 20px;
            margin: 30px 0;
            font-style: italic;
            color: #666;
        }
        
        .blog-article-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        @media (max-width: 768px) {
            .blog-article-title {
                font-size: 28px;
            }
            
            .blog-article-content {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>Blog</h1>
        </div>
    </div>

    <!-- Blog Article -->
    <article class="blog-article">
        <div class="container">
            <div class="blog-article-header">
                <a href="blog.php" class="blog-back">&larr; Volver al Blog</a>
                <span class="blog-date">
                    <?php 
                    $date = new DateTime($post['published_at']);
                    echo $date->format('d \d\e F, Y'); 
                    ?>
                </span>
            </div>
            
            <h1 class="blog-article-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <?php if ($post['featured_image']): ?>
            <div class="blog-article-image">
                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
            </div>
            <?php endif; ?>
            
            <div class="blog-article-content">
                <?php echo $post['content']; ?>
            </div>
            
            <div class="blog-article-footer">
                <a href="blog.php" class="btn btn-primary">Ver más artículos</a>
            </div>
        </div>
    </article>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
