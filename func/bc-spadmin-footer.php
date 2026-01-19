
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.all.min.js"></script>

<?php if(isset($_SESSION["product_purchase_response"])){ ?>


<script>
  Swal.fire ('Message!', '<?php echo $_SESSION["product_purchase_response"]; ?>', 'success') ;
            //Swal.fire({ position:'top-end',type:'',title:'Oops', text: 'kindly fill all form', showConfirmButton:1,timer:2500 });
          setTimeout(() => {
                fetch('/func/unset-product-response.php')
                    .then(response => response.text());
            }, 1000); // 3 seconds

</script>


	<!-- <div style="text-align: center; max-height: 40%;" id="customAlertDiv" class="bg-2 box-shadow m-z-index-2 s-z-index-2 m-scroll-auto s-scroll-auto m-block-dp s-block-dp m-position-fix s-position-fix m-top-20 s-top-30 br-radius-5px m-width-60 s-width-26 m-height-auto s-height-auto m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-padding-tp-5 s-padding-tp-1 m-padding-bm-5 s-padding-bm-1 m-margin-lt-19 s-margin-lt-26 m-margin-bm-2 s-margin-bm-2">
	<span style="user-select: auto; word-break: break-word;" class="color-10 text-bold-500 m-font-size-15 s-font-size-18">
		<?php echo $_SESSION["product_purchase_response"]; ?>
	</span><br/>
	<button style="text-align: center; user-select: auto;" onclick="customDismissPop();" onkeypress="keyCustomDismissPop(event);" class="button-box br-radius-50 onhover-bg-color-10 a-cursor color-2 bg-10 m-font-size-10 s-font-size-10 br-style-tp-0 m-inline-dp s-inline-block-dp m-bottom-0 s-bottom-5 m-position-sti s-position-sti m-width-30 s-width-30 m-height-auto s-height-auto m-margin-tp-1 s-margin-tp-1 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-5 s-padding-lt-5 m-padding-rt-5 s-padding-rt-5">
		DISMISS
	</button>
</div>
<script>
	function customDismissPop(){
		var customAlertDiv = document.getElementById("customAlertDiv");
		setTimeout(function(){
			customAlertDiv.style.display = "none";
		}, 300);
	}
	
	document.addEventListener("keydown", function(event){
		if(event.keyCode === 13){
			//prevent enter key default function
			event.preventDefault();
			var customAlertDiv = document.getElementById("customAlertDiv");
			setTimeout(function(){
				customAlertDiv.style.display = "none";
			}, 300);
		}
	});
	
	clearProductResponse();
	function clearProductResponse(){
		var productHttp = new XMLHttpRequest();
        productHttp.open("GET", "../unset-product.php");
        productHttp.setRequestHeader("Content-Type", "application/json");
        // productHttp.onload = function(){
        //     alert(productHttp.status);
        // }
        productHttp.send();
	}
</script> -->
<?php } ?>

<div class="w-100 mh-25 mt-3 d-block d-xl-none d-md-none d-lg-none" style="height: 80px;"></div>
</main>

<div class="bg-white w-100 mh-25 py-2 position-fixed border bottom-0 d-flex flex-row justify-items-center justify-content-between d-block d-xl-none d-md-none d-lg-none" style="height: 80px;">
  
  <div class="col-2 d-flex flex-column align-items-center">
      <button class="btn btn-secondary rounded-circle mb-2" page-name="Airtime.php">
        <a href="<?php echo $web_http_host; ?>/web/Airtime.php" class="text-decoration-none">
          <i class="bi bi-telephone text-white"></i>
        </a>
      </button>
      <span class="text-dark h6">Airtime</span>
  </div>

<div class="col-2 d-flex flex-column align-items-center">
      <button class="btn btn-secondary rounded-circle mb-2" page-name="Data.php">
        <a href="<?php echo $web_http_host; ?>/web/Data.php" class="text-decoration-none">
          <i class="bi bi-wifi text-white"></i>
        </a>
      </button>
      <span class="text-dark h6">Data</span>
  </div>

<div class="col-2 d-flex flex-column align-items-center">
      <button class="btn btn-secondary rounded-circle mb-2" page-name="Dashboard.php">
        <a href="<?php echo $web_http_host; ?>/web/Dashboard.php" class="text-decoration-none">
          <i class="bi bi-grid text-white"></i>
        </a>
      </button>
      <span class="text-dark h6">Dashboard</span>
  </div>

<div class="col-2 d-flex flex-column align-items-center">
      <button class="btn btn-secondary rounded-circle mb-2" page-name="Cable.php">
        <a href="<?php echo $web_http_host; ?>/web/Cable.php" class="text-decoration-none">
          <i class="bi bi-tv text-white"></i>
        </a>
      </button>
      <span class="text-dark h6">Cable</span>
  </div>

  <div class="col-2 d-flex flex-column align-items-center">
      <button class="btn btn-secondary rounded-circle mb-2" page-name="Electric.php">
        <a href="<?php echo $web_http_host; ?>/web/Electric.php" class="text-decoration-none">
          <i class="bi bi-lightbulb text-white"></i>
        </a>
      </button>
      <span class="text-dark h6">Electric</span>
  </div>

  
</div>

<script src="/jsfile/bc-custom-all.js"></script>
<script src="/jsfile/bc-custom-vendor.js"></script>
<script src="/jsfile/bc-custom-super.js"></script>

  <!-- Vendor JS Files -->
  <script src="../assets-2/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets-2/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets-2/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets-2/vendor/echarts/echarts.min.js"></script>
  <script src="../assets-2/vendor/quill/quill.js"></script>
  <script src="../assets-2/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets-2/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets-2/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets-2/js/main.js"></script>

<?php mysqli_close($connection_server); ?>