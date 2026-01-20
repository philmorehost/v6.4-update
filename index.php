<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Initialize variables to be used in the template
$whatsapp_number = '';
$header_image_url = '';

// Check if vendor details are available from index.php
if (isset($vendor_account_details) && is_array($vendor_account_details)) {
    // Format the phone number for WhatsApp
    if (!empty($vendor_account_details['phone_number'])) {
        $phone = preg_replace('/[^0-9]/', '', $vendor_account_details['phone_number']);
        if (substr($phone, 0, 1) === '0') {
            $whatsapp_number = '234' . substr($phone, 1);
        } elseif (substr($phone, 0, 3) === '234') {
            $whatsapp_number = $phone;
        }
    }

    // Function to get the header image (can be kept here or moved to a central function file)
    function get_header_image($connection, $vendor_id) {
        if (!$connection || !$vendor_id) return '';
        $template_name = '%template-1%';
        $stmt = mysqli_prepare($connection, "SELECT header_image FROM sas_vendor_style_templates WHERE vendor_id = ? AND template_name LIKE ?");
        mysqli_stmt_bind_param($stmt, "is", $vendor_id, $template_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $image = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $image['header_image'] ?? '';
    }

    // Get the header image URL
    if ($connection_server) { // Ensure connection is available
        $header_image_url = get_header_image($connection_server, $vendor_account_details["id"]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Your one-stop platform for airtime, data, bill payments, and financial services in Nigeria.">
    <meta name="author" content="BeeCodes Titan">
    
    <title><?php echo htmlspecialchars($vendor_account_details['firstname'] ?? 'VTU Platform'); ?> - Instant Airtime, Data, and Bill Payments</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        /* All the styles from your previous refactored page are here */
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fe; color: #4A5568; }
        .navbar { background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .navbar-brand img { height: 45px; }
        .nav-link { font-weight: 500; color: #2D3748 !important; }
        .nav-link:hover, .nav-link.active { color: #4A90E2 !important; }
        .btn { font-weight: 500; padding: 10px 25px; border-radius: 50px; transition: all 0.3s; }
        .btn-primary { background-color: #4A90E2; border-color: #4A90E2; }
        .btn-primary:hover { background-color: #357ABD; border-color: #357ABD; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(74, 144, 226, 0.3); }
        .btn-outline-primary { border-color: #4A90E2; color: #4A90E2; }
        .btn-outline-primary:hover { background-color: #4A90E2; color: #fff; }
        .hero-section { background: linear-gradient(rgba(20, 25, 40, 0.7), rgba(20, 25, 40, 0.7)), url('/uploaded-image/<?php echo $header_image_url; ?>') center/cover no-repeat; padding: 120px 0; color: #fff; text-align: center; }
        .hero-section h1 { font-size: 3.5rem; font-weight: 700; }
        .hero-section p { font-size: 1.25rem; font-weight: 300; max-width: 600px; margin: 20px auto 40px; }
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { font-weight: 600; color: #1A202C; }
        .section-title p { color: #718096; }
        .service-card { background: #fff; border-radius: 15px; padding: 30px; text-align: center; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07); transition: all 0.3s; height: 100%; }
        .service-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(74, 144, 226, 0.2); }
        .service-card .icon { font-size: 40px; width: 80px; height: 80px; line-height: 80px; margin: 0 auto 25px; border-radius: 50%; background: linear-gradient(135deg, #4A90E2, #7B68EE); color: #fff; }
        .service-card h5 { font-weight: 600; margin-bottom: 15px; color: #1A202C; }
        .service-card p { font-size: 0.95rem; color: #718096; }
        footer { background-color: #1A202C; color: #A0AEC0; padding: 60px 0 20px; }
        footer h5 { color: #fff; }
        footer a { color: #A0AEC0; text-decoration: none; }
        footer a:hover { color: #fff; }
        .sub-footer { border-top: 1px solid #2D3748; padding-top: 20px; margin-top: 40px; text-align: center; }
        .btn-whatsapp { background-color: #25D366; border-color: #25D366; color: #fff; }
        .btn-whatsapp:hover { background-color: #1DA851; border-color: #1DA851; }
    </style>
</head>

<body>

    <!-- ***** Header Area Start ***** -->
    <header>
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="/uploaded-image/<?php echo str_replace(['.',':'],'-',$_SERVER['HTTP_HOST']).'_'; ?>logo.png" alt="Company Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" href="#welcome">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    </ul>
                    <div class="ms-lg-3">
                        <a href="/web/Dashboard.php" class="btn btn-outline-primary me-2">Login</a>
                        <a href="/web/Register.php" class="btn btn-primary">Register</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <!-- ***** Header Area End ***** -->

    <!-- ***** Welcome Area (Hero Section) ***** -->
    <section class="hero-section" id="welcome">
        <div class="container">
            <h1>Digital Payments Made Simple</h1>
            <p>Your reliable platform for instant Airtime, Data, TV subscriptions, Electricity bills, and much more. All in one place.</p>
            <a href="/web/Register.php" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </section>

    <!-- ***** Services Section ***** -->
    <section id="services" class="py-5 my-5">
        <div class="container">
            <div class="section-title">
                <h2>All Your Daily Needs, One Platform</h2>
                <p>We offer a wide range of services to make your life easier and more connected.</p>
            </div>
            <div class="row g-4">
                 <?php
                $services = [
                    ['icon' => 'fas fa-mobile-alt', 'title' => 'Buy Airtime', 'desc' => 'Instantly top up your phone with airtime on any network (MTN, Glo, Airtel, 9mobile) with great discounts.'],
                    ['icon' => 'fas fa-wifi', 'title' => 'Buy Data', 'desc' => 'Get affordable and fast data bundles for all networks to stay connected to the internet anytime, anywhere.'],
                    ['icon' => 'fas fa-lightbulb', 'title' => 'Buy Electricity', 'desc' => 'Conveniently pay your prepaid or postpaid electricity bills online and get your token instantly.'],
                    ['icon' => 'fas fa-tv', 'title' => 'Cable TV', 'desc' => 'Renew your DSTV, GOtv, and StarTimes subscriptions effortlessly and never miss your favorite shows.'],
                    ['icon' => 'fas fa-sms', 'title' => 'Bulk SMS', 'desc' => 'Send customized bulk SMS to multiple contacts for your events or business marketing with high delivery rates.'],
                    ['icon' => 'fas fa-graduation-cap', 'title' => 'Exam Pin', 'desc' => 'Purchase WAEC, NECO, and NABTEB result checker pins and registration e-pins with ease.'],
                    ['icon' => 'fas fa-print', 'title' => 'Recharge Card Printing', 'desc' => 'Start your own business by printing and selling recharge card e-pins for all mobile networks in Nigeria.'],
                    ['icon' => 'fas fa-sim-card', 'title' => 'Data Card Printing', 'desc' => 'Generate and print data bundle pins for all networks, providing a valuable service to your customers.'],
                    ['icon' => 'fas fa-gift', 'title' => 'Gift Cards', 'desc' => 'Trade your local and international gift cards for instant cash at the best market rates.'],
                    ['icon' => 'fas fa-credit-card', 'title' => 'Virtual Cards', 'desc' => 'Create secure Virtual Dollar and Naira cards for your international and local online payments.'],
                    ['icon' => 'fas fa-globe-africa', 'title' => 'Global Money Transfer', 'desc' => 'Send and receive money from family and friends across the globe quickly and securely.'],
                ];

                foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="icon"><i class="<?php echo $service['icon']; ?>"></i></div>
                        <h5><?php echo $service['title']; ?></h5>
                        <p><?php echo $service['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ***** NEW WHATSAPP CONTACT SECTION START ***** -->
    <section id="contact" class="py-5" style="background-color: #fff;">
        <div class="container">
            <div class="section-title">
                <h2>Get In Touch</h2>
                <p>Have a question or need support? Send us a message directly on WhatsApp!</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form id="whatsappForm">
                        <div class="mb-3">
                            <label for="waName" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="waName" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <label for="waMessage" class="form-label">Your Message</label>
                            <textarea class="form-control" id="waMessage" rows="5" placeholder="How can we help you today?" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-whatsapp btn-lg">
                                <i class="fab fa-whatsapp me-2"></i>Send on WhatsApp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** NEW WHATSAPP CONTACT SECTION END ***** -->

    <!-- ***** Footer ***** -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5>About Us</h5>
                    <p>We are a comprehensive bill payment infrastructure dedicated to providing fast, reliable, and cost-effective digital services for your everyday needs.</p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#services">Services</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="/web/Dashboard.php">Login</a></li>
                        <li><a href="/web/Register.php">Register</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                     <h5>Contact Us</h5>
                     <?php if($vendor_account_details): ?>
                        <p><i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($vendor_account_details["home_address"]); ?></p>
                        <p><i class="fas fa-envelope me-2"></i> <a href="mailto:<?php echo htmlspecialchars($vendor_account_details["email"]); ?>"><?php echo htmlspecialchars($vendor_account_details["email"]); ?></a></p>
                        <p><i class="fas fa-phone me-2"></i> <a href="tel:+<?php echo $whatsapp_number; ?>">+<?php echo $whatsapp_number; ?></a></p>
                    <?php endif; ?>
                </div>
                 <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="sub-footer">
                <p>Copyright &copy; <script>document.write(new Date().getFullYear())</script> <?php echo htmlspecialchars(ucwords(strtolower($_SERVER["HTTP_HOST"]))); ?>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- WhatsApp Form Handler Script -->
    <script>
        document.getElementById('whatsappForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Stop the form from submitting normally

            // Get the vendor's phone number from PHP
            const vendorPhoneNumber = '<?php echo $whatsapp_number; ?>';

            // Check if the phone number is available
            if (!vendorPhoneNumber) {
                alert('The site owner has not configured their WhatsApp number. Please try again later.');
                return;
            }

            // Get form values
            const name = document.getElementById('waName').value;
            const message = document.getElementById('waMessage').value;

            // Create the pre-filled message
            const fullMessage = `Hello, my name is ${name}.\n\n${message}`;

            // Encode the message for the URL
            const encodedMessage = encodeURIComponent(fullMessage);

            // Create the WhatsApp URL
            const whatsappUrl = `https://wa.me/${vendorPhoneNumber}?text=${encodedMessage}`;

            // Open WhatsApp in a new tab
            window.open(whatsappUrl, '_blank');
        });
    </script>
</body>
</html>