	
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
      <a href="#" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block"><img src="<?php echo $web_http_host; ?>/uploaded-image/<?php echo str_replace(['.',':'],'-',$_SERVER['HTTP_HOST']).'_'; ?>logo.png" alt=""></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
</div>


    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

       

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">1</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              Latest Notification
              <!-- <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a> -->
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li>
          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo'/asset/boy-icon.png'; ?>"" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">
              <?php echo $_SESSION["spadmin_session"]; ?>
            </span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION["spadmin_session"]; ?></h6>
              
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $web_http_host; ?>/bc-spadmin/MarketPlace.php">
                <i class="bi bi-shop"></i>
                <span>MarketPlace</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $web_http_host; ?>/bc-spadmin/AccountSettings.php">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/spadmin-logout.php'}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Log Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
    <?php if(isset($_SESSION["spadmin_session"]) && isset($get_logged_spadmin_details)){ ?>
      <li class="nav-item">
        <a class="nav-link " href="<?php echo $web_http_host; ?>/bc-spadmin/Dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span> 
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#manage-vendor-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Manage Vendors</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="manage-vendor-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/VendorReg.php">
              <i class="bi bi-circle"></i><span>Add Vendor</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/Vendors.php">
              <i class="bi bi-circle"></i><span>View Vendors</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/VendorRegistrations.php">
              <i class="bi bi-circle"></i><span>Pending Registrations</span>
            </a>
          </li>
        </ul>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#manage-billing-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Manage Billings</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="manage-billing-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/BillingPackages.php">
              <i class="bi bi-circle"></i><span>Billing Packages</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/CreateBilling.php">
              <i class="bi bi-circle"></i><span>Add Billing</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/Billings.php">
              <i class="bi bi-circle"></i><span>View Billings</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/AllSubscriptions.php">
              <i class="bi bi-circle"></i><span>All Subscriptions</span>
            </a>
          </li>
        </ul>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#manage-api-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Manage API</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="manage-api-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/CreateAPI.php">
              <i class="bi bi-circle"></i><span>Add API</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/MarketPlace.php">
              <i class="bi bi-circle"></i><span>View MarketPlace</span>
            </a>
          </li>
        </ul>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#manage-notify-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Manage Notification</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="manage-notify-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/StatusMessage.php">
              <i class="bi bi-circle"></i><span>Update Status Message</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-spadmin/SendMail.php">
              <i class="bi bi-circle"></i><span>Send Mail</span>
            </a>
          </li>
        </ul>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/bc-spadmin/PaymentGateway.php">
          <i class="bi bi-credit-card-fill"></i>
          <span>Payment Gateway</span> 
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/bc-spadmin/ShareFund.php">
          <i class="bi bi-cash"></i>
          <span>Fund Vendor</span> 
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/bc-spadmin/Transactions.php">
          <i class="bi bi-wallet"></i>
          <span>Transactions</span> 
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/bc-spadmin/PaymentOrders.php">
          <i class="bi bi-cart-fill"></i>
          <span>Payment Orders</span> 
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/bc-spadmin/EmailTemplates.php">
          <i class="bi bi-envelope-at-fill"></i>
          <span>Email Templates</span> 
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/bc-spadmin/SubscriptionReports.php">
          <i class="bi bi-bar-chart-line"></i>
          <span>Reports</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/bc-spadmin/AccountSettings.php">
          <i class="bi bi-gear"></i>
          <span>Account Settings</span> 
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/bc-spadmin/DomainSettings.php">
          <i class="bi bi-globe"></i>
          <span>Domain Settings</span>
        </a>
      </li>

      
      <?php } ?>
  </aside><!-- End Sidebar--> 
  
   <main id="main" class="main">