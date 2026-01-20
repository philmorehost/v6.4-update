	
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
      <a href="#" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block"><img src="<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); echo $web_http_host; ?>/uploaded-image/<?php echo str_replace(['.',':'],'-',$_SERVER['HTTP_HOST']).'_'; ?>logo.png" alt=""></span>
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
              <?php echo $_SESSION["admin_session"]; ?>
            </span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION["admin_session"]; ?></h6>
              
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $web_http_host; ?>/bc-admin/MarketPlace.php">
                <i class="bi bi-shop"></i>
                <span>MarketPlace</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $web_http_host; ?>/bc-admin/AccountSettings.php">
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
              <a class="dropdown-item d-flex align-items-center" onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/admin-logout.php'}">
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
    <?php if(isset($_SESSION["admin_session"]) && isset($get_logged_admin_details)){ ?>
      <li class="nav-item">
        <a class="nav-link " href="<?php echo $web_http_host; ?>/bc-admin/Dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span> 
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#manage-user-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Manage User</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="manage-user-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/CreateUser.php">
              <i class="bi bi-circle"></i><span>Create User</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Users.php">
              <i class="bi bi-circle"></i><span>Users</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Transactions.php">
              <i class="bi bi-circle"></i><span>Transactions</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/BatchTransactions.php">
              <i class="bi bi-circle"></i><span>Batch Transactions</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/PaymentOrders.php">
              <i class="bi bi-circle"></i><span>Payment Orders</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/FundTransferRequests.php">
              <i class="bi bi-circle"></i><span>Fund Transfer Requests</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/ShareFund.php">
              <i class="bi bi-circle"></i><span>Fund User</span>
            </a>
          </li>
        </ul>
        </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#system-func-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>System Function</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="system-func-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/AccountSettings.php">
              <i class="bi bi-circle"></i><span>Account Settings</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Fund.php">
              <i class="bi bi-circle"></i><span>Fund Wallet</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/StatusMessage.php">
              <i class="bi bi-circle"></i><span>Status Message</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/EmailTemplates.php">
              <i class="bi bi-circle"></i><span>Email Templates</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SendMail.php">
              <i class="bi bi-circle"></i><span>Mail Sender</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/IDBlockingSystem.php">
              <i class="bi bi-circle"></i><span>ID Blocking System</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SalesCalculator.php">
              <i class="bi bi-circle"></i><span>Sales Calculator</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SenderIDRequests.php">
              <i class="bi bi-circle"></i><span>Sender ID Requests</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SelfTransactions.php">
              <i class="bi bi-circle"></i><span>My Transactions</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SelfSubmitPayment.php">
              <i class="bi bi-circle"></i><span>My Submit Payment</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SelfPaymentOrders.php">
              <i class="bi bi-circle"></i><span>My Payment Orders</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/PaymentGateway.php">
              <i class="bi bi-circle"></i><span>Payment Gateway</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/PaymentLink.php">
              <i class="bi bi-circle"></i><span>Payment Links</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/BankTransfer.php">
              <i class="bi bi-circle"></i><span>Bank Transfer</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/CoinSettings.php">
              <i class="bi bi-circle"></i><span>Coin Settings</span>
            </a>
          </li>
        </ul>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#api-manager-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>API Manager</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="api-manager-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/MarketPlace.php">
              <i class="bi bi-circle"></i><span>MarketPlace</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/ProductSetUp.php">
              <i class="bi bi-circle"></i><span>Product SetUp</span>
            </a>
          </li>
          
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Crypto.php">
              <i class="bi bi-circle"></i><span>Crypto</span>
            </a>
          </li>
          
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Airtime.php">
              <i class="bi bi-circle"></i><span>Airtime</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/IntlAirtime.php">
              <i class="bi bi-circle"></i><span>Int'l Airtime</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/BulkSMS.php">
              <i class="bi bi-circle"></i><span>Bulk SMS</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SharedData.php">
              <i class="bi bi-circle"></i><span>SharedData</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SmeData.php">
              <i class="bi bi-circle"></i><span>Sme Data</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/CorporateData.php">
              <i class="bi bi-circle"></i><span>Corporate Data</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/DirectData.php">
              <i class="bi bi-circle"></i><span>Direct Data</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Electric.php">
              <i class="bi bi-circle"></i><span>Electric</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Betting.php">
              <i class="bi bi-circle"></i><span>Betting</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Exam.php">
              <i class="bi bi-circle"></i><span>Exam</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Cable.php">
              <i class="bi bi-circle"></i><span>Cable</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Datacard.php">
              <i class="bi bi-circle"></i><span>Datacard</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/Rechargecard.php">
              <i class="bi bi-circle"></i><span>Rechargecard</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/NairaCard.php">
              <i class="bi bi-credit-card-2-front"></i><span>Naira Card</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/DollarCard.php">
              <i class="bi bi-credit-card-2-front"></i><span>Dollar Card</span>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#subscription-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gem"></i><span>Subscription Manager</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="subscription-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/RenewSubscription.php">
              <i class="bi bi-circle"></i><span>Renew Subscription</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/SubscriptionHistory.php">
              <i class="bi bi-circle"></i><span>Subscription History</span>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#blog-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Blog Manager</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="blog-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/BlogPosts.php">
              <i class="bi bi-circle"></i><span>Posts</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/bc-admin/BlogCategories.php">
              <i class="bi bi-circle"></i><span>Categories</span>
            </a>
          </li>
        </ul>
      </li></main>
      <?php } ?>
  </aside><!-- End Sidebar--> 
  
   <main id="main" class="main">