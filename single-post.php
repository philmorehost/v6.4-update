<?php
    // --- DATABASE CONNECTION & VENDOR DETAILS ---
    function get_db_connection() {
        include("func/bc-connect.php");
        return $connection_server;
    }

    $connection_server = get_db_connection();
    $vendor_account_details = null;

    if ($connection_server) {
        $host = $_SERVER["HTTP_HOST"];
        $stmt_vendor = mysqli_prepare($connection_server, "SELECT * FROM sas_vendors WHERE website_url = ? AND status = 1 LIMIT 1");
        mysqli_stmt_bind_param($stmt_vendor, "s", $host);
        mysqli_stmt_execute($stmt_vendor);
        $result_vendor = mysqli_stmt_get_result($stmt_vendor);
        if ($row = mysqli_fetch_assoc($result_vendor)) {
            $vendor_account_details = $row;
        }
        mysqli_stmt_close($stmt_vendor);
    }

    // --- FETCH SINGLE POST ---
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: blog.php");
        exit();
    }
    $post_id = (int)$_GET['id'];

    $stmt_post = mysqli_prepare($connection_server, "SELECT p.*, v.firstname, v.lastname FROM blog_posts p JOIN sas_super_admin v ON p.author_id = v.id WHERE p.id = ? AND p.status = 'published'");
    mysqli_stmt_bind_param($stmt_post, "i", $post_id);
    mysqli_stmt_execute($stmt_post);
    $post_res = mysqli_stmt_get_result($stmt_post);

    if (mysqli_num_rows($post_res) === 0) {
        header("Location: blog.php");
        exit();
    }
    $post = mysqli_fetch_assoc($post_res);

    // --- FETCH RELATED POSTS ---
    $related_posts = [];
    $cat_stmt = mysqli_prepare($connection_server, "SELECT category_id FROM blog_post_categories WHERE post_id = ?");
    mysqli_stmt_bind_param($cat_stmt, "i", $post_id);
    mysqli_stmt_execute($cat_stmt);
    $categories_result = mysqli_stmt_get_result($cat_stmt);
    $category_ids = [];
    while($cat = mysqli_fetch_assoc($categories_result)) {
        $category_ids[] = $cat['category_id'];
    }
    mysqli_stmt_close($cat_stmt);

    if (!empty($category_ids)) {
        $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
        $related_sql = "
            SELECT p.id, p.title, p.featured_image, p.created_at
            FROM blog_posts p
            JOIN blog_post_categories pc ON p.id = pc.post_id
            WHERE pc.category_id IN ($placeholders) AND p.id != ? AND p.status = 'published'
            GROUP BY p.id
            ORDER BY p.created_at DESC
            LIMIT 2"; // Limit to 2 for a cleaner two-column layout

        $related_stmt = mysqli_prepare($connection_server, $related_sql);
        $types = str_repeat('i', count($category_ids)) . 'i';
        $params = array_merge($category_ids, [$post_id]);
        mysqli_stmt_bind_param($related_stmt, $types, ...$params);
        mysqli_stmt_execute($related_stmt);
        $related_posts_res = mysqli_stmt_get_result($related_stmt);
        if($related_posts_res) {
            while($rel_post = mysqli_fetch_assoc($related_posts_res)) {
                $related_posts[] = $rel_post;
            }
        }
        mysqli_stmt_close($related_stmt);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo htmlspecialchars($vendor_account_details['firstname'] ?? 'Blog'); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fe;
            color: #4A5568;
        }
        .navbar {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .navbar-brand img { height: 45px; }
        .nav-link { font-weight: 500; color: #2D3748 !important; }
        .nav-link:hover, .nav-link.active { color: #4A90E2 !important; }
        .btn-primary { background-color: #4A90E2; border-color: #4A90E2; border-radius: 50px; }
        .btn-primary:hover { background-color: #357ABD; border-color: #357ABD; }
        .btn-outline-primary { border-color: #4A90E2; color: #4A90E2; border-radius: 50px; }
        .btn-outline-primary:hover { background-color: #4A90E2; color: #fff; }

        .post-header {
            position: relative;
            padding: 180px 0 80px;
            text-align: center;
            color: #fff;
            background-size: cover;
            background-position: center;
            border-radius: 0 0 30px 30px;
            overflow: hidden;
        }
        .post-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(rgba(20, 25, 40, 0.8), rgba(20, 25, 40, 0.6));
        }
        .post-header .container { position: relative; }
        .post-header h1 {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .post-meta {
            font-size: 1rem;
        }

        .post-content-area {
            margin-top: -50px; /* Pulls content up over the header's rounded corner */
            position: relative;
            z-index: 2;
        }
        .post-content-card {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .post-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }
        .post-body img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 25px 0;
        }
        
        .related-posts-section { margin-top: 60px; }
        .related-posts-title { font-weight: 600; margin-bottom: 30px; }
        .related-post-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.07);
            transition: all 0.3s ease;
        }
        .related-post-card:hover { transform: translateY(-5px); }
        .related-post-card img { height: 200px; object-fit: cover; }
        .related-post-card .card-body { padding: 20px; }
        .related-post-card .card-title a { color: #1A202C; text-decoration: none; font-weight: 600; font-size: 1rem; }
        .related-post-card .card-title a:hover { color: #4A90E2; }
        
        footer {
            background-color: #1A202C;
            color: #A0AEC0;
            padding: 60px 0 20px;
            margin-top: 80px;
        }
        footer h5 { color: #fff; }
        footer a { color: #A0AEC0; text-decoration: none; }
        footer a:hover { color: #fff; }
        .sub-footer {
            border-top: 1px solid #2D3748;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="/uploaded-image/<?php echo str_replace(['.',':'],'-',$_SERVER['HTTP_HOST']).'_'; ?>logo.png" alt="Company Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="index.php#welcome">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#about">About</a></li>
                        <li class="nav-item"><a class="nav-link active" href="blog.php">Blog</a></li>
                    </ul>
                    <div class="ms-lg-3">
                        <a href="/web/Dashboard.php" class="btn btn-outline-primary me-2">Login</a>
                        <a href="/web/Register.php" class="btn btn-primary">Register</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section class="post-header" style="background-image: url('<?php echo htmlspecialchars($post['featured_image']); ?>');">
        <div class="container">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="post-meta">
                <span><i class="fas fa-user"></i> By <?php echo htmlspecialchars($post['firstname'] . ' ' . $post['lastname']); ?></span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <span><i class="fas fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
            </div>
        </div>
    </section>
    
    <section class="post-content-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="post-content-card">
                        <div class="post-body">
                            <?php echo base64_decode($post['content']); ?>
                        </div>
                    </div>

                    <?php if (!empty($related_posts)): ?>
                    <div class="related-posts-section">
                        <h3 class="related-posts-title">You Might Also Like</h3>
                        <div class="row">
                            <?php foreach($related_posts as $rel_post): ?>
                            <div class="col-md-6">
                                <div class="card related-post-card">
                                    <a href="single-post.php?id=<?php echo $rel_post['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($rel_post['featured_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($rel_post['title']); ?>">
                                    </a>
                                    <div class="card-body">
                                        <h5 class="card-title"><a href="single-post.php?id=<?php echo $rel_post['id']; ?>"><?php echo htmlspecialchars($rel_post['title']); ?></a></h5>
                                        <p class="card-text"><small class="text-muted"><?php echo date('F j, Y', strtotime($rel_post['created_at'])); ?></small></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
             <div class="row">
                <div class="col-lg-12">
                    <div class="sub-footer">
                        <p>Copyright &copy; <script>document.write(new Date().getFullYear())</script> <?php echo htmlspecialchars(ucwords(strtolower($_SERVER["HTTP_HOST"]))); ?>. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>