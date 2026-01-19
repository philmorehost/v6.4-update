<?php session_start();
    include("./func/bc-spadmin-config.php");
    if($connection){
    	header("Location: /bc-spadmin");
    }
    if(isset($_POST["setup"])){
    	$host = trim(strip_tags($_POST["host"]));
    	$dbname = trim(strip_tags($_POST["dbname"]));
    	$user = trim(strip_tags($_POST["user"]));
    	$pass = trim(strip_tags($_POST["pass"]));
    	if(!empty($host) && !empty($dbname) && !empty($user)){
    		$db_json_text = 
    		'<?php'."\n".'	$db_json_dtls = array("server" => "'.$host.'", "user" => "'.$user.'", "pass" => "'.$pass.'", "dbname" => "'.$dbname.'");'."\n".'	$db_json_encode = json_encode($db_json_dtls,true);'."\n".'	$db_json_decode = json_decode($db_json_encode,true);'."\n".'?>';
    		if(file_exists("./func/db-json.php")){
    			file_put_contents("./func/db-json.php", $db_json_text);
    			//Database Information Updated Successfully
    			$json_response_array = array("desc" => "Database Information Updated Successfully");
    			$json_response_encode = json_encode($json_response_array,true);
    		}else{
    			fopen("./func/db-json.php", "a++");
    			file_put_contents("./func/db-json.php", $db_json_text);
    			//Database Information Created Successfully
    			$json_response_array = array("desc" => "Database Information Created Successfully");
    			$json_response_encode = json_encode($json_response_array,true);
    		}
    	}else{
    		if(empty($host)){
	    		//Host Address Field Empty
    			$json_response_array = array("desc" => $host."Host Address Field Empty");
    			$json_response_encode = json_encode($json_response_array,true);
    		}else{
    			if(empty($dbname)){
    				//Database Name Field Empty
    				$json_response_array = array("desc" => "Database Name Field Empty");
    				$json_response_encode = json_encode($json_response_array,true);
    			}else{
    				if(empty($user)){
    					//Username Field Empty
    					$json_response_array = array("desc" => "Username Field Empty");
    					$json_response_encode = json_encode($json_response_array,true);
    				}else{
    					/*if(empty($pass)){
    						//Password Field Empty
    						$json_response_array = array("desc" => "Password Field Empty");
    						$json_response_encode = json_encode($json_response_array,true);
    					}*/
    				}
    			}
    		}
    	}
		
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
?>
<!DOCTYPE html>
<head>
	<title>Database Setup</title>
    <meta charset="UTF-8" />
    <meta name="description" content="" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    <style>
    	body{
    		background-color: var(--color-5);
    	}
    </style>
</head>
<body>
    <div style="text-align: center;" class="bg-10 m-block-dp s-block-dp m-position-abs s-position-abs br-radius-5px m-width-94 s-width-50 m-height-auto s-height-auto m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-padding-tp-25 s-padding-tp-3 m-padding-bm-25 s-padding-bm-3 m-margin-tp-20 s-margin-tp-5 m-margin-lt-2 s-margin-lt-24">
        <img src="<?php echo $web_http_host; ?>/uploaded-image/sp-logo.png" style="user-select: auto; object-fit: contain; object-position: center;" class="a-cursor m-position-rel s-position-rel m-inline-block-dp s-inline-block-dp m-width-30 s-width-30 m-height-30 s-height-30 m-margin-tp-0 s-margin-tp-0 m-margin-bm-3 s-margin-bm-2"/><br/>
        <span style="user-select: auto;" class="text-bg-1 color-4 m-inline-block-dp s-inline-block-dp text-bold-500 m-font-size-20 s-font-size-25 m-margin-bm-2 s-margin-bm-2">DATABASE SET-UP</span><br>
        <form method="post" action="">
            <input style="text-align: center; text-transform: lowercase;" name="host" type="text" value="<?php echo $mySqlServer; ?>" placeholder="Host Address" class="input-box outline-none color-4 bg-2 m-inline-block-dp s-inline-block-dp outline-none br-radius-5px br-width-4 br-color-4 m-width-60 s-width-45 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-margin-bm-1 s-margin-bm-1" autocomplete="on" required/><br/>
            <input style="text-align: center; text-transform: lowercase;" name="dbname" type="text" value="<?php echo $mySqlDBName; ?>" placeholder="Database Name" class="input-box outline-none color-4 bg-2 m-inline-block-dp s-inline-block-dp outline-none br-radius-5px br-width-4 br-color-4 m-width-60 s-width-45 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-margin-bm-1 s-margin-bm-1" autocomplete="on" required/><br/>
            <input style="text-align: center; text-transform: lowercase;" name="user" type="text" value="<?php echo $mySqlUser; ?>" placeholder="Username" class="input-box outline-none color-4 bg-2 m-inline-block-dp s-inline-block-dp outline-none br-radius-5px br-width-4 br-color-4 m-width-60 s-width-45 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-margin-bm-1 s-margin-bm-1" autocomplete="on" required/><br/>
            <input style="text-align: center;" name="pass" type="password" value="" placeholder="********" pattern="{8,}" title="Password must be atleast 8 character long" class="input-box outline-none color-4 bg-2 m-inline-block-dp s-inline-block-dp outline-none br-radius-5px br-width-4 br-color-4 m-width-60 s-width-45 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-margin-bm-1 s-margin-bm-1" autocomplete="off" /><br/>
            <button id="" name="setup" type="submit" style="user-select: auto;" class="button-box a-cursor outline-none color-2 bg-7 m-inline-block-dp s-inline-block-dp outline-none onhover-bg-color-5 br-radius-5px br-width-4 br-color-4 m-width-63 s-width-47 m-padding-tp-2 s-padding-tp-1 m-padding-bm-2 s-padding-bm-1 m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-margin-bm-2 s-margin-bm-2" >
                SET-UP
            </button><br/>
            
        </form>
    </div>

<?php if(isset($_SESSION["product_purchase_response"])){ ?>
<div style="text-align: center;" id="customAlertDiv" class="bg-2 box-shadow m-z-index-2 s-z-index-2 m-block-dp s-block-dp m-position-fix s-position-fix m-top-20 s-top-40 br-radius-5px m-width-60 s-width-26 m-height-auto s-height-auto m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-padding-tp-5 s-padding-tp-1 m-padding-bm-5 s-padding-bm-1 m-margin-lt-19 s-margin-lt-36 m-margin-bm-2 s-margin-bm-2">
	<span style="user-select: notne;" class="color-10 text-bold-500 m-font-size-20 s-font-size-25">
		<?php echo $_SESSION["product_purchase_response"]; ?>
	</span><br/>
	<button style="text-align: center; user-select: auto;" onclick="customDismissPop();" onkeypress="keyCustomDismissPop(event);" class="button-box onhover-bg-color-10 a-cursor color-2 bg-10 m-font-size-12 s-font-size-13 br-style-tp-0 m-inline-dp s-inline-block-dp m-position-rel s-position-rel m-width-30 s-width-30 m-height-auto s-height-auto m-margin-tp-1 s-margin-tp-1 m-margin-bm-2 s-margin-bm-2 m-margin-lt-0 s-margin-lt-0 m-margin-rt-0 s-margin-rt-0 m-padding-tp-5 s-padding-tp-5 m-padding-bm-5 s-padding-bm-5 m-padding-lt-5 s-padding-lt-5 m-padding-rt-5 s-padding-rt-5">
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
</script>
<?php } ?>
</body>
</html>