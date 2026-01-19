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
        $stmt = mysqli_prepare($connection_server, "SELECT * FROM sas_vendors WHERE website_url = ? AND status = 1 LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $host);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $vendor_account_details = $row;
        }
        mysqli_stmt_close($stmt);
    }

    // --- PAGINATION & POST FETCHING LOGIC ---
    $limit = 5; // Number of posts per page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page > 0) ? ($page - 1) * $limit : 0;

    // Get total number of published posts
    $total_posts_res = mysqli_query($connection_server, "SELECT COUNT(id) as count FROM blog_posts WHERE status = 'published'");
    $total_posts = mysqli_fetch_assoc($total_posts_res)['count'];
    $total_pages = ceil($total_posts / $limit);

    // Securely fetch posts for the current page
    $posts_query = "
        SELECT p.*, v.firstname, v.lastname 
        FROM blog_posts p 
        JOIN sas_super_admin v ON p.author_id = v.id 
        WHERE p.status = 'published' 
        ORDER BY p.created_at DESC 
        LIMIT ? OFFSET ?
    ";
    $posts_stmt = mysqli_prepare($connection_server, $posts_query);
    mysqli_stmt_bind_param($posts_stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($posts_stmt);
    $posts_result = mysqli_stmt_get_result($posts_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Blog - <?php echo htmlspecialchars($vendor_account_details['firstname'] ?? 'Our'); ?> Updates</title>

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
        .navbar-brand img {
            height: 45px;
        }
        .nav-link {
            font-weight: 500;
            color: #2D3748 !important;
            transition: color 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: #4A90E2 !important;
        }
        .btn-primary {
            background-color: #4A90E2;
            border-color: #4A90E2;
            font-weight: 500;
            padding: 10px 25px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #357ABD;
            border-color: #357ABD;
            transform: translateY(-2px);
        }
        .btn-outline-primary {
             border-color: #4A90E2;
             color: #4A90E2;
             font-weight: 500;
             padding: 10px 25px;
             border-radius: 50px;
        }
        .btn-outline-primary:hover {
            background-color: #4A90E2;
            color: #fff;
        }

        .page-header {
            background: linear-gradient(135deg, #4A90E2, #7B68EE);
            padding: 140px 0 60px;
            text-align: center;
            color: #fff;
        }
        .page-header h1 {
            font-weight: 700;
        }

        .blog-section {
            padding: 60px 0;
        }
        .blog-post-card {
            background: #fff;
            border: none;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.07);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .blog-post-card:hover { 
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(74, 144, 226, 0.2);
        }
        .blog-post-card .post-image img { 
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        .blog-post-card .post-content { padding: 30px; }
        .blog-post-card .post-title a {
            font-size: 1.75rem;
            color: #1A202C;
            text-decoration: none;
            font-weight: 600;
            line-height: 1.3;
        }
        .blog-post-card .post-title a:hover { color: #4A90E2; }
        .blog-post-card .post-meta { 
            font-size: 0.9rem;
            color: #718096;
            margin: 15px 0;
        }
        .blog-post-card .post-meta .author {
            font-weight: 500;
            color: #4A5568;
        }
        .blog-post-card .post-excerpt {
            color: #4A5568;
            font-size: 1rem;
            line-height: 1.7;
        }
        .blog-post-card .read-more {
            color: #4A90E2;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            display: inline-block;
        }
        .read-more:hover {
            text-decoration: underline;
        }

        .pagination .page-link {
            border-radius: 50px !important;
            margin: 0 5px;
            border: 1px solid #dee2e6;
            color: #4A90E2;
        }
        .pagination .page-item.active .page-link {
            background-color: #4A90E2;
            border-color: #4A90E2;
            color: #fff;
            box-shadow: 0 4px 10px rgba(74, 144, 226, 0.3);
        }
        
        footer {
            background-color: #1A202C;
            color: #A0AEC0;
            padding: 60px 0 20px;
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

    <section class="page-header">
        <div class="container">
            <h1>Our Blog</h1>
            <p class="lead">News, updates, and insights from our team.</p>
        </div>
    </section>
    
    <section class="blog-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if ($posts_result && mysqli_num_rows($posts_result) > 0): ?>
                        <?php while($post = mysqli_fetch_assoc($posts_result)): ?>
                            <div class="blog-post-card">
                                <?php if(!empty($post['featured_image'])): ?>
                                <div class="post-image">
                                    <a href="single-post.php?id=<?php echo $post['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    </a>
                                </div>
                                <?php endif; ?>
                                <div class="post-content">
                                    <h2 class="post-title"><a href="single-post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                                    <p class="post-meta">
                                        <i class="fas fa-user"></i> By <span class="author"><?php echo htmlspecialchars($post['firstname'] . ' ' . $post['lastname']); ?></span>
                                        &nbsp;&nbsp;|&nbsp;&nbsp;
                                        <i class="fas fa-calendar-alt"></i> <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                    </p>
                                    <p class="post-excerpt">
                                        <?php 
                                            // Safely decode and create an excerpt
                                            $content_decoded = base64_decode($post['content']);
                                            $content_plain = strip_tags($content_decoded);
                                            echo substr($content_plain, 0, 250) . (strlen($content_plain) > 250 ? '...' : ''); 
                                        ?>
                                    </p>
                                    <a href="single-post.php?id=<?php echo $post['id']; ?>" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <h3>No posts found.</h3>
                            <p>Check back later for news and updates!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
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