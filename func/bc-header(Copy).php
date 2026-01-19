	<script>
		function dropDropMenu(htmlElement){
			var htmlElementTag = htmlElement;
			var htmlElementID = htmlElementTag.id;
			var dropDownBtn = document.getElementsByClassName(htmlElementID+"-btn");
			var dropDownBtnImg = document.getElementsByClassName(htmlElementID+"-img");
			var dropDownItem = document.getElementsByClassName(htmlElementID);
			var dropDownItemButton = document.getElementsByClassName(htmlElementID+"-item");
			var dropDownMenuABtn = document.getElementsByClassName("dropdown-menu-a-btn");
			
			for(let i = 0; i < dropDownMenuABtn.length;  i++){
				if(dropDownMenuABtn[i].id != htmlElementID){
					var dropDownMenuItem = document.getElementsByClassName(dropDownMenuABtn[i].id);
					if(dropDownMenuItem[i].style.display == "block"){
						document.getElementById(dropDownMenuABtn[i].id).click();
					}
					
				}
			}
			
			for(let i = 0; i < dropDownItem.length;  i++){
				if(dropDownItem[i].style.display == "none"){
					if(dropDownItemButton[i].classList.contains("bg-4")){
						dropDownItemButton[i].classList.remove("color-2");
						dropDownItemButton[i].classList.add("color-6");
						dropDownItemButton[i].classList.remove("bg-4");
						dropDownItemButton[i].classList.add("bg-7");
						dropDownItemButton[i].classList.remove("onhover-bg-color-7");
						dropDownItemButton[i].classList.add("onhover-bg-color-4");
					}
					dropDownItem[i].style.display = "block";
					dropDownBtnImg[0].src = "/asset/drop-up.png";
				}else{
					if(dropDownItemButton[i].classList.contains("bg-2")){
						dropDownItemButton[i].classList.remove("color-6");
						dropDownItemButton[i].classList.add("color-2");
						dropDownItemButton[i].classList.remove("bg-7");
						dropDownItemButton[i].classList.add("bg-4");
						dropDownItemButton[i].classList.remove("onhover-bg-color-4");
						dropDownItemButton[i].classList.add("onhover-bg-color-7");
					}
					dropDownItem[i].style.display = "none";
					dropDownBtnImg[0].src = "/asset/drop-down.png";
				}
			}
			
		}
	</script>
	
	<div style="height: 70px;" class="color-4 bg-2 m-z-index-5 s-z-index-5 m-position-fix s-position-fix m-top-0 s-top-0 m-flex-column-dp s-flex-row-dp m-width-100 s-width-100 m-padding-tp-2 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1">
        <div class="bg-3 m-position-rel s-position-rel m-inline-block-dp s-inline-block-dp m-width-100 s-width-14 m-height-100 s-height-90" >
        	<img alt="Menu" onclick="toggleSlider();" id="toggleSlider" src="<?php echo $web_http_host; ?>/asset/open-black.png" style="user-select: auto; object-fit: contain; object-position: center;" class="a-cursor m-position-rel s-position-abs m-inline-block-dp s-inline-block-dp m-width-6 s-width-0 m-height-100 s-height-0 m-top-5 s-top-2 m-margin-tp-0 s-margin-tp-0 m-margin-lt-7 s-margin-lt-0"/>
        	<img alt="Logo" src="<?php echo $web_http_host; ?>/uploaded-image/<?php echo str_replace(['.',':'],'-',$_SERVER['HTTP_HOST']).'_'; ?>logo.png" style="user-select: auto; object-fit: contain; object-position: left;" class="m-position-rel s-position-abs m-inline-block-dp s-inline-block-dp m-width-80 s-width-95 m-height-100 s-height-100 m-margin-tp-0 s-margin-tp-5 m-margin-lt-1 s-margin-lt-5"/>
 
        </div>
		
        <div style="text-align: center;" class="color-4 bg-3 m-position-rel s-position-rel m-scroll-x s-scroll-x m-scroll-nowrap s-scroll-nowrap m-none-dp s-inline-block-dp m-width-0 s-width-64 m-height-0 s-height-100 m-margin-lt-0 s-margin-lt-1 m-margin-rt-0 s-margin-rt-0">
            <a href="<?php echo $web_http_host; ?>/web/Dashboard.php" class="">
                <button style="user-select: auto;" class="button-box a-cursor text-bold-600 onhover-bg-color-9 color-4 bg-10 br-radius-5px m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-height-70 s-height-50 m-top-13 s-top-25 m-margin-tp-0 s-margin-tp-0 m-margin-bm-0 s-margin-bm-0 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-2 s-padding-lt-1 m-padding-rt-2 s-padding-rt-1">
                    DASHBOARD
                </button>
            </a>

            <a href="<?php echo $web_http_host; ?>/web/Airtime.php" class="">
                <button style="user-select: auto;" class="button-box a-cursor text-bold-600 onhover-bg-color-9 color-4 bg-10 br-radius-5px m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-height-70 s-height-50 m-top-13 s-top-25 m-margin-tp-0 s-margin-tp-0 m-margin-bm-0 s-margin-bm-0 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-2 s-padding-lt-1 m-padding-rt-2 s-padding-rt-1">
                    AIRTIME
                </button>
            </a>
            
            <a href="<?php echo $web_http_host; ?>/web/Data.php" class="">
                <button style="user-select: auto;" class="button-box a-cursor text-bold-600 onhover-bg-color-9 color-4 bg-10 br-radius-5px m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-height-70 s-height-50 m-top-13 s-top-25 m-margin-tp-0 s-margin-tp-0 m-margin-bm-0 s-margin-bm-0 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-2 s-padding-lt-1 m-padding-rt-2 s-padding-rt-1">
                    INTERNET DATA
                </button>
            </a>
            
              <a href="<?php echo $web_http_host; ?>/web/Betting.php" class="">
                <button style="user-select: auto;" class="button-box a-cursor text-bold-600 onhover-bg-color-9 color-4 bg-10 br-radius-5px m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-height-70 s-height-50 m-top-13 s-top-25 m-margin-tp-0 s-margin-tp-0 m-margin-bm-0 s-margin-bm-0 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-2 s-padding-lt-1 m-padding-rt-2 s-padding-rt-1">
                    BETTING
                </button>
            </a>
            
            <a href="<?php echo $web_http_host; ?>/web/Card.php" class="">
                <button style="user-select: auto;" class="button-box a-cursor text-bold-600 onhover-bg-color-9 color-4 bg-10 br-radius-5px m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-height-70 s-height-50 m-top-13 s-top-25 m-margin-tp-0 s-margin-tp-0 m-margin-bm-0 s-margin-bm-0 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-2 s-padding-lt-1 m-padding-rt-2 s-padding-rt-1">
                    PRINT CARD
                </button>
            </a>
            
            <a href="<?php echo $web_http_host; ?>/web/Cable.php" class="">
                <button style="user-select: auto;" class="button-box a-cursor text-bold-600 onhover-bg-color-9 color-4 bg-10 br-radius-5px m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-height-70 s-height-50 m-top-13 s-top-25 m-margin-tp-0 s-margin-tp-0 m-margin-bm-0 s-margin-bm-0 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-2 s-padding-lt-1 m-padding-rt-2 s-padding-rt-1">
                    CABLE
                </button>
            </a>
            
            <a href="<?php echo $web_http_host; ?>/web/Electric.php" class="">
                <button style="user-select: auto;" class="button-box a-cursor text-bold-600 onhover-bg-color-9 color-4 bg-10 br-radius-5px m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-height-70 s-height-50 m-top-13 s-top-25 m-margin-tp-0 s-margin-tp-0 m-margin-bm-0 s-margin-bm-0 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-2 s-padding-lt-1 m-padding-rt-2 s-padding-rt-1">
                    ELECTRICITY
                </button>
            </a>
            
            <a href="<?php echo $web_http_host; ?>/web/APIDocs.php" class="">
                <button style="user-select: auto;" class="button-box a-cursor text-bold-600 onhover-bg-color-9 color-4 bg-10 br-radius-5px m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-height-70 s-height-50 m-top-13 s-top-25 m-margin-tp-0 s-margin-tp-0 m-margin-bm-0 s-margin-bm-0 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-2 s-padding-lt-1 m-padding-rt-2 s-padding-rt-1">
                    API DOCUMENTATION
                </button>
            </a>
            
        </div>
        <div style="text-align: left;" class="color-4 bg-3 m-position-rel s-position-rel m-block-dp s-inline-block-dp m-width-0 s-width-18 m-height-0 s-height-100 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-lt-0 s-padding-lt-1 m-padding-rt-0 s-padding-rt-1">
            <div style="text-align: center; user-select: auto; word-break: break-all;" class=" text-bold-600 color-4 bg-3 text-bold-300 m-font-size-14 s-font-size-16 m-scroll-none s-scroll-none m-none-dp s-inline-block-dp m-width-0 s-width-80 m-height-0 s-height-30 m-position-rel s-position-rel m-top-2 s-top-21 m-margin-tp-0 s-margin-tp-0 m-margin-lt-0 s-margin-lt-0">Hello, <?php if(isset($get_logged_user_details)){ echo strtoupper($get_logged_user_details["username"]); }else{ echo "Guest"; } ?></div>
			<div style="text-align: center; user-select: auto;" class=" text-bold-600 color-2 bg-3 text-bold-300 m-font-size-14 s-font-size-16 m-scroll-none s-scroll-none m-none-dp s-inline-block-dp m-width-0 s-width-15 m-height-0 s-height-50 m-position-rel s-position-rel m-top-2 s-top-26 m-margin-tp-0 s-margin-tp-0 m-margin-lt-0 s-margin-lt-0"><a href="AccountSettings.php" title="Profile"><img class="bg-2 br-radius-50 m-width-0 s-width-90" src="<?php echo'/asset/boy-icon.png'; ?>" /></a></div>
		</div>
    </div>
    <div style="height: 70px;" class="color-4 bg-3 m-width-100 s-width-100 m-padding-tp-2 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1"></div>
	
	<div style="height: calc(95% - 70px); text-align: center;" id="headerSliderDiv" class="bg-10 m-z-index-4 s-z-index-none m-inline-block-dp s-inline-block-dp m-width-0 s-width-20 m-position-fix s-position-fix m-scroll-y s-scroll-y m-scroll-initial s-scroll-initial m-float-none s-float-lt m-clr-float-none s-clr-float-lt m-padding-tp-1 s-padding-tp-1 m-padding-bm-5 s-padding-bm-1">	
		<?php if(isset($_SESSION["user_session"]) && isset($get_logged_user_details)){ ?>
		<a href="<?php echo $web_http_host; ?>/web/Dashboard.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/home-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> DASHBOARD
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/Fund.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/add-fund.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> FUND WALLET
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/Airtime.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/airtime-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> BUY AIRTIME
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/Data.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/internet-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> BUY DATA
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/BulkAirtime.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/airtime-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> BULK AIRTIME
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/BulkData.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/internet-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> BULK DATA
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/Electric.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/electricity-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> ELECTRIC
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/Exam.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/exam-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> EXAM PIN
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/Cable.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/cable-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> CABLE TV
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/Betting.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/print-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> BETTING
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/Card.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/print-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> PRINT CARD
			</button>
		</a>
		
		<a href="<?php echo $web_http_host; ?>/web/BulkSMS.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/sms-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> BULK SMS
			</button>
		</a>
		
		<!-- MANAGEMENT -->
		<a href="#" onclick="dropDropMenu(this);" id="dropdown-management" class="dropdown-menu-a-btn">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-btn text-bold-800 button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				MANAGEMENT <img src="<?php echo $web_http_host; ?>/asset/drop-down.png" style="pointer-events: none; object-fit: contain; float: right; clear: both;" class="dropdown-management-img m-width-10 s-width-20 s-height-1rem s-height-1rem"/>
			</button>
		</a>
		
		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/ShareFund.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/share-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> SHARE FUND
			</button>
		</a>
		
		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/ShareFundHistory.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/share-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> SHARE FUND (HISTORY)
			</button>
		</a>

		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/PaymentOrders.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/trans-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> PAYMENT ORDERS
			</button>
		</a>

		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/SubmitPayment.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/cart-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> SUBMIT PAYMENT
			</button>
		</a>

		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/Transactions.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/trans-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> TRANSACTIONS
			</button>
		</a>

		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/BatchTransactions.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/trans-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> BATCH TRANSACTIONS
			</button>
		</a>

		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/TransactionCalculator.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/trans-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> TRANSACTION CALCULATOR
			</button>
		</a>

		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/Pricing.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/trans-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> PRICING
			</button>
		</a>
		
		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/SubmitSenderID.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/sms-icon.svg" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> SUBMIT SENDER ID
			</button>
		</a>
		
		<a style="display: none;" href="<?php echo $web_http_host; ?>/web/APIDocs.php" class="dropdown-management">
			<button style="text-align: left; user-select: auto;" class="dropdown-management-item button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/developer-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> API DOCS
			</button>
		</a>

		<a href="<?php echo $web_http_host; ?>/web/AccountSettings.php" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/setting-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> ACCOUNT SETTINGS
			</button>
		</a>

		<a onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/logout.php'}" class="">
			<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
				<img src="<?php echo $web_http_host; ?>/asset/logout-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> LOGOUT
			</button>
		</a>
		<?php }else{ ?>
			<a href="/web/Login.php" class="">
				<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
					<img src="/asset/user-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> LOGIN
				</button>
			</a>
			
			<a href="/web/Register.php" class="">
				<button style="text-align: left; user-select: auto;" class="button-box onhover-bg-color-5 a-cursor color-2 bg-3 br-color-2 br-style-bm-1 br-width-1 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-95 s-width-95 m-height-auto s-height-auto m-margin-tp-0 s-margin-tp-0 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-15 s-padding-lt-15 m-padding-rt-5 s-padding-rt-5">
					<img src="/asset/user-icon.png" style="pointer-events: none; object-fit: contain;" class="m-width-10 s-width-20 s-height-1rem s-height-1rem"/> REGISTER
				</button>
			</a>
		<?php } ?>
	</div>
	
	<div onclick="toggleSlider();" style="height: calc(95% - 70px);" id="bodyOpacityDiv" class="bg-3 m-z-index-0 s-z-index-0 m-block-dp s-none-dp m-width-100 s-width-80 m-position-fix s-position-sti m-scroll-y s-scroll-y m-scroll-initial s-scroll-initial m-float-none s-float-rt m-clr-float-none s-clr-float-rt m-padding-tp-1 s-padding-tp-1"></div>
	<div style="height: calc(95% - 70px);" id="bodyDiv" class="bg-3 m-z-index-1 s-z-index-0 m-block-dp s-inline-block-dp m-width-100 s-width-80 m-position-fix s-position-sti m-scroll-y s-scroll-y m-scroll-initial s-scroll-initial m-float-none s-float-rt m-clr-float-none s-clr-float-rt m-padding-tp-1 s-padding-tp-1">	