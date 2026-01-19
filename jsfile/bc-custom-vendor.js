
    function getWebApikey(getElement) {
        var getWebApikey = getElement;
        var webApikeyStatus = document.getElementById("web-apikey-status");
        var webApikeyInput = document.getElementById("web-apikey-input");
        webApikeyInput.value = getWebApikey[getWebApikey.selectedIndex].getAttribute("api-key");
        
        for(x=0; x < webApikeyStatus.options.length; x++){
        	if(webApikeyStatus.options[x].value == getWebApikey[getWebApikey.selectedIndex].getAttribute("api-status")){
        		webApikeyStatus.options[x].selected = true;
        	}
        }
    }
	
	
	function vPCheckoutRef(){
		var checkoutRef = document.getElementById("num-ref");
		if(checkoutRef.value.length < 1){
			var selectUserHttp = new XMLHttpRequest();
			selectUserHttp.open("GET", "../random-vpaid.php");
			selectUserHttp.setRequestHeader("Content-Type", "application/json");
			selectUserHttp.onload = function(){
				if((selectUserHttp.readyState === 4) && (selectUserHttp.status === 200)){
					var jsonDecoded = JSON.parse(selectUserHttp.responseText);
					var jsonText = jsonDecoded.text;
					if(jsonDecoded.status == 200){
						checkoutRef.value = jsonText;
					}else{
						//checkoutRef.value = jsonText;
					}
				}else{
					//checkoutRef.value = "System Error";
				}
			}
			selectUserHttp.send();
		}
	}
	
	function vtickPaymentGateway(getElement, networkName, productID, buttonID, fileExt){
	var getElementFeature = getElement;
	var ispNames = getElementFeature.getAttribute("product-name-array").replaceAll(" ","").split(",");
	var productName = document.getElementById(productID);
	
	if(fileExt.trim() !== ""){
	fileExt = fileExt;
	}else{
	fileExt = "png";
	}
	
	for(x=0; x < ispNames.length; x++){
	var notTickedService = document.getElementById(ispNames[x]+"-lg");
	
	if(ispNames[x] !== networkName){
	if(networkName.trim().length >= 1){
	notTickedService.src = "/asset/"+ispNames[x]+"."+fileExt;
	notTickedService.style = "filter: grayscale(100%);";
	notTickedService.classList.remove("br-radius-5px");
	notTickedService.classList.add("br-radius-100px");
	productName.value = networkName;
	}else{
	notTickedService.src = "/asset/"+ispNames[x]+"."+fileExt;
	notTickedService.classList.remove("br-radius-5px");
	notTickedService.classList.add("br-radius-100px");
	productName.value = networkName;
	}
	}else{
	if(networkName.trim().length >= 1){
	notTickedService.src = "/asset/"+ispNames[x]+"-marked."+fileExt;
	notTickedService.style = "filter: grayscale(0%);";
	notTickedService.classList.remove("br-radius-100px");
	notTickedService.classList.add("br-radius-5px");
	}else{
	notTickedService.src = "/asset/"+ispNames[x]+"-marked."+fileExt;
	notTickedService.classList.remove("br-radius-100px");
	notTickedService.classList.add("br-radius-5px");
	}
	}
	}
	if(ispNames.indexOf(networkName) !== -1){
	vcheckPaymentGatewayDetails(buttonID,"1");
	}
	}
	
	function vcheckPaymentGatewayDetails(buttonID,funID){
	var fundAmount = document.getElementById("fund-amount");
	var productName = document.getElementById("gatewayname");
	var installProduct = document.getElementById(buttonID);
	var productStatus = document.getElementById("product-status-span");
	vPCheckoutRef();
	
	if(funID.trim() == 1){
	if(Number(fundAmount.value) && (fundAmount.value > 0) && (fundAmount.value >= 100) && (fundAmount.value.length >= 1) && (productName.value.length >= 1)){
	var gatewayFunc = productName.value + "PaymentGateway();";
	installProduct.setAttribute("onclick", gatewayFunc);
	installProduct.style.pointerEvents = "auto";
	}else{
	installProduct.style.pointerEvents = "none";
	}
	setInterval(() => {
	document.getElementById("amount-to-pay").value = 0;
	document.getElementById("gateway-public").value = "";
	document.getElementById("gateway-encrypt").value = "";
	
	var getProductTag_2 = document.getElementById(productName.value + "-lg");
	var amountToPay_2 = parseInt(fundAmount.value)+(fundAmount.value*(getProductTag_2.getAttribute("gateway-int")/100));
	var gateway_public_key = getProductTag_2.getAttribute("gateway-public");
	var gateway_encrypt_key = getProductTag_2.getAttribute("gateway-encrypt");
	if(Number(fundAmount.value) && Number(getProductTag_2.getAttribute("gateway-int"))){
	productStatus.innerHTML = "Amount To Pay is N"+amountToPay_2;
	document.getElementById("amount-to-pay").value = amountToPay_2;
	document.getElementById("gateway-public").value = gateway_public_key;
	document.getElementById("gateway-encrypt").value = gateway_encrypt_key;
	
	}else{
	productStatus.innerHTML = "Amount To Pay is N0";
	}
	});
	}else{
	if(funID.trim() == 2){
	var getProductTag = document.getElementById(productName.value + "-lg");
	getProductTag.click();
	}else{
	
	}
	}
	}
	
    function tickProduct(getElement, networkName, productID, buttonID, fileExt){
        var getElementFeature = getElement;
        var ispNames = getElementFeature.getAttribute("product-name-array").replaceAll(" ","").split(",");
        var productName = document.getElementById(productID);
        var installProduct = document.getElementById(buttonID);
        if(fileExt.trim() !== ""){
            fileExt = fileExt;
        }else{
            fileExt = "png";
        }
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            if(!notTickedService) continue;
            
            if(ispNames[x] !== networkName){
                if(networkName.trim().length >= 1){
                    notTickedService.src = "/asset/"+ispNames[x]+"."+fileExt;
                    notTickedService.style = "filter: grayscale(100%);";
                    notTickedService.classList.remove("br-radius-5px");
                    notTickedService.classList.add("br-radius-100px");
                }else{
                    notTickedService.src = "/asset/"+ispNames[x]+"."+fileExt;
                    notTickedService.classList.remove("br-radius-5px");
                    notTickedService.classList.add("br-radius-100px");
                }
            }else{
                if(networkName.trim().length >= 1){
                    notTickedService.src = "/asset/"+ispNames[x]+"-marked."+fileExt;
                    notTickedService.style = "filter: grayscale(0%);";
                    notTickedService.classList.remove("br-radius-100px");
                    notTickedService.classList.add("br-radius-5px");
                }else{
                    notTickedService.src = "/asset/"+ispNames[x]+"-marked."+fileExt;
                    notTickedService.classList.remove("br-radius-100px");
                    notTickedService.classList.add("br-radius-5px");
                }
            }
        }
        if(ispNames.indexOf(networkName) !== -1){
            if(productName) productName.value = networkName;
            if(installProduct) installProduct.style.pointerEvents = "auto";
        }else{
            if(productName) productName.value = "";
            if(installProduct) installProduct.style.pointerEvents = "none";
        }
    }
    
    function adminCardsSwitch(){
        var cardIsp = document.getElementById("admin-cards-isp");
        var cardQty = document.getElementById("admin-cards-qty");
        
        if(cardIsp.value.trim() != ""){
        	for(x=0; x < cardQty.options.length; x++){
        		if(cardQty.options[x].getAttribute("product-isp") != null){
        			if(cardQty.options[x].getAttribute("product-isp") == cardIsp.value.trim()){
        				cardQty.options[x].hidden = false;
                        
        					var cardCategoryId = cardIsp.value.trim()+"_"+cardQty.value.trim();
        			}else{
        				cardQty.options[x].hidden = true;
        			}
        		}
                if((x + 1) == cardQty.options.length){
                    adminCardsPopulateValue(cardCategoryId);
                }
        	}
        }
    }

    function adminCardsPopulateValue(cardCategoryId){
        var cardLists = document.getElementById("admin-cards-textarea");
		var cardListsDialCode = document.getElementById("admin-cards-input");
		
        var cardCategoryList = document.getElementById(cardCategoryId);
        var cardCategoryListDialCode = document.getElementById(cardCategoryId+"_dial_code");
        
        cardLists.value = cardCategoryList.value;
        cardListsDialCode.value = cardCategoryListDialCode.value;
    }

    function adminCardsSwitchReset(){
        var cardQty = document.getElementById("admin-cards-qty");
        var cardLists = document.getElementById("admin-cards-textarea");
        cardLists.value = "";
        for(x=0; x < cardQty.options.length; x++){
            if(cardQty.options[x].value == ""){
                cardQty.options[x].hidden = true;
                cardQty.options[x].selected = true;
            }
        }
    }
    
    function adminConfirmUser(){
        var username = document.getElementById("share-fund-user").value;
        
        var userStatus = document.getElementById("user-status-span");
        var selectUserHttp = new XMLHttpRequest();
        selectUserHttp.open("POST", "../select-user.php");
        selectUserHttp.setRequestHeader("Content-Type", "application/json");
        var selectUserHttpBody = JSON.stringify({user: username, request_sender: 'admin'});
        selectUserHttp.onload = function(){
            if((selectUserHttp.readyState === 4) && (selectUserHttp.status === 200)){
                var jsonDecoded = JSON.parse(selectUserHttp.responseText);
                if(username.trim() !== ""){
                    if(jsonDecoded.status == 200){
                        userStatus.innerHTML = jsonDecoded.text;
    					adminUserVerification(true);
                    }else{
                        userStatus.innerHTML = jsonDecoded.text;
                        adminUserVerification(false);
                    }
                }else{
                    userStatus.innerHTML = "Enter User ID";
                    adminUserVerification(false);
                }
            }else{
                userStatus.innerHTML = "System Error: Cannot Verify User";
                adminUserVerification(false);
            }
        }
        selectUserHttp.send(selectUserHttpBody);
    }
    
    function adminUserVerification(userAccountStatus){
    	var amount = document.getElementById("share-fund-amount").value.trim();
    	var proceedBtn = document.getElementById("proceedBtn");
    	
    	if((userAccountStatus == true) && Number(amount) && (amount.length >= 2) && (amount >= 10) && (amount <= 1999999)){
    		proceedBtn.style = "pointer-events: auto;";
    	}else{
    		proceedBtn.style = "pointer-events: none;";
    	}
    }
    
    function spAdminConfirmVendor(){
        var vendorname = document.getElementById("share-fund-vendor").value;
        
        var vendorStatus = document.getElementById("vendor-status-span");
        var selectVendorHttp = new XMLHttpRequest();
        selectVendorHttp.open("POST", "../select-vendor.php");
        selectVendorHttp.setRequestHeader("Content-Type", "application/json");
        var selectVendorHttpBody = JSON.stringify({vendor: vendorname, request_sender: 'spadmin'});
        selectVendorHttp.onload = function(){
            if((selectVendorHttp.readyState === 4) && (selectVendorHttp.status === 200)){
                var jsonDecoded = JSON.parse(selectVendorHttp.responseText);
                if(vendorname.trim() !== ""){
                    if(jsonDecoded.status == 200){
                        vendorStatus.innerHTML = jsonDecoded.text;
                        spAdminVendorVerification(true);
                    }else{
                        vendorStatus.innerHTML = jsonDecoded.text;
                        spAdminVendorVerification(false);
                    }
                }else{
                    vendorStatus.innerHTML = "Enter Vendor ID";
                    spAdminVendorVerification(false);
                }
            }else{
                vendorStatus.innerHTML = "System Error: Cannot Verify Vendor";
                spAdminVendorVerification(false);
            }
        }
        selectVendorHttp.send(selectVendorHttpBody);
    }
    
    function spAdminVendorVerification(vendorAccountStatus){
        var amount = document.getElementById("share-fund-amount").value.trim();
        var proceedBtn = document.getElementById("proceedBtn");
        
        if((vendorAccountStatus == true) && Number(amount) && (amount.length >= 2) && (amount >= 10) && (amount <= 1999999)){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }
    
    function adminPaymentOrderStatus(status, reference, user){
    	var statusArray = ["1","2"];
    	var actionArray = {"1":"reject", "2":"accept"};
    	if(Number(status) && (statusArray.indexOf(status) !== -1)){
    		if(confirm("Are you sure you want to "+actionArray[status]+" "+user+"'s payment order?")){
    			window.location.href = "/bc-admin/PaymentOrders.php?order-status="+status+"&order-ref="+reference;
    		}else{
    			alert("Action Cancelled");
    		}
    	}else{
    		alert("Invalid Payment Status");
    	}
    }

    function adminFundTransferRequestStatus(status, reference, user){
    	var statusArray = ["1","2","3"];
    	var actionArray = {"1":"reject", "2":"accept", "3":"reject with no refund for"};
    	if(Number(status) && (statusArray.indexOf(status) !== -1)){
    		if(confirm("Are you sure you want to "+actionArray[status]+" "+user+"'s fund transfer order?")){
    			window.location.href = "/bc-admin/FundTransferRequests.php?order-status="+status+"&order-ref="+reference;
    		}else{
    			alert("Action Cancelled");
    		}
    	}else{
    		alert("Invalid Payment Status");
    	}
    }

    function adminSenderIDStatus(status, senderID, user){
    	var statusArray = ["1","2","3"];
    	var actionArray = {"1":"reject", "2":"accept", "3":"disable"};
    	if(Number(status) && (statusArray.indexOf(status) !== -1)){
    		if(confirm("Are you sure you want to "+actionArray[status]+" "+user+"'s Sender ID Request?")){
    			window.location.href = "/bc-admin/SenderIDRequests.php?sender-id-status="+status+"&sender-id="+senderID;
    		}else{
    			alert("Action Cancelled");
    		}
    	}else{
    		alert("Invalid ID Status");
    	}
    }
    
    function updateUserAccountStatus(status, user){
    	var statusArray = ["1","2","3"];
    	var actionArray = {"1":"activate", "2":"block", "3":"delete"};
    	if(Number(status) && (statusArray.indexOf(status) !== -1)){
    		if(confirm("Are you sure you want to "+actionArray[status]+" "+user+"'s account?")){
    			window.location.href = "/bc-admin/Users.php?account-status="+status+"&account-username="+user;
    		}else{
    			alert("Action Cancelled");
    		}
    	}else{
    		alert("Invalid Account Status");
    	}
    }

    function updateUserAccountAPIStatus(status, user){
    	var statusArray = ["1","2"];
    	var actionArray = {"1":"activate", "2":"block"};
    	if(Number(status) && (statusArray.indexOf(status) !== -1)){
    		if(confirm("Are you sure you want to "+actionArray[status]+" "+user+"'s account API Gateway?")){
    			window.location.href = "/bc-admin/Users.php?account-api-status="+status+"&account-username="+user;
    		}else{
    			alert("Action Cancelled");
    		}
    	}else{
    		alert("Invalid Account API Status");
    	}
    }

    function loginUserAccount(idNumber, user){
        if(Number(idNumber) && (idNumber >= 1)){
            if(user.trim() !== ""){
                if(confirm("Are you sure you want to Login "+user+"'s account?")){
                    window.location.href = "/bc-admin/Users.php?account-log="+idNumber;
                }else{
                    alert("Action Cancelled");
                }
            }else{
                alert("Error: User is missing");
            }
    	}else{
    		alert("Error: Login Failed! Invalid Account ID");
    	}
    }
    
    function getCookieDet(cookieName){
        var getCookie = document.cookie;
        var decodeCookie = decodeURIComponent(getCookie);
        var explodeCookie = decodeCookie.split(";");
        var cookieDetail = "";
        for(i=0; i < explodeCookie.length; i++){
            reExplodeEach = explodeCookie[i].split("=");
            if(reExplodeEach[0].includes(cookieName)){
                cookieDetail += reExplodeEach[1];
            }
        }
        
        if(cookieDetail.trim() == ""){
            return false;
        }else{
            return cookieDetail;
        }
    }

    //Count Cart Items
    setInterval(function(){
        var countCartItems = document.getElementById("count-cart-items");
        var getCartSpans = document.getElementsByClassName("cart-spans");
        
        var cartUserID = getCartSpans[0].id.replaceAll("cart-", "").split("-")[1];
		var cookieName = window.location.hostname.replaceAll(".","_").replaceAll(":","_") + "_" + cartUserID + "_cart_items";
		
		var getAllCookieReturnVals = getCookieDet(cookieName);
		if(getAllCookieReturnVals == false){
			document.cookie = cookieName + "=;";
			getAllCookieReturnVals = "";
		}else{
			getAllCookieReturnVals = getAllCookieReturnVals;
		}
		var explodeCookieReturnVals = getAllCookieReturnVals.split(" ");
		
        var countItem = 0;
        if(getAllCookieReturnVals.trim() !== ""){
        	for(x=0; x < explodeCookieReturnVals.length; x++){
        		countItem++;
        	}
        }
        countCartItems.innerHTML = countItem;
    }, 1000);
    
    //Reselect Cart Item on Reload
    setTimeout(function(){
        var getCartSpans = document.getElementsByClassName("cart-spans");
        var getCookie = document.cookie;
        var decodeCookie = decodeURIComponent(getCookie);
        for(x=0; x < getCartSpans.length; x++){
            var cartSpanID = getCartSpans[x].id.replaceAll("cart-", "").split("-");
            var cartItemID = cartSpanID[0];
            var cartUserID = cartSpanID[1];
            var cookieName = window.location.hostname.replaceAll(".","_").replaceAll(":","_") + "_" + cartUserID + "_cart_items";
            
            var getAllCookieReturnVals = getCookieDet(cookieName);
            var explodeCookieReturnVals = getAllCookieReturnVals.split(" ");
            
            var checkItemExists = explodeCookieReturnVals.indexOf(cartItemID);
            if(checkItemExists !== -1){
                getCartSpans[x].innerHTML = "Remove Cart";
            }else{
                getCartSpans[x].innerHTML = "Add Cart";
            }
        }
    }, 100);
    
    //Add/Remove Item to Cart
    function addAPIToCart(getCurrentElement, itemID, userID){
        var getCurrentElementTag = getCurrentElement;
        var cartDetailID = itemID + "-" + userID;
        var cookieName = window.location.hostname.replaceAll(".","_").replaceAll(":","_") + "_" + userID + "_cart_items";
        var getAllCookieReturnVals = getCookieDet(cookieName);
        if(getAllCookieReturnVals == false){
        	document.cookie = cookieName + "=;";
        	getAllCookieReturnVals = "";
        }else{
        	getAllCookieReturnVals = getAllCookieReturnVals;
        }
        var explodeCookieReturnVals = getAllCookieReturnVals.split(" ");
        
        var checkItemExists = explodeCookieReturnVals.indexOf(itemID);

        if(checkItemExists == -1){
            addCartItemToBox(cartDetailID);
            getCurrentElementTag.innerHTML = "Remove Cart";
        }else{
            if(confirm("Do you want to remove this item from your cart?")){
                removeCartItemToBox(cartDetailID);
                getCurrentElementTag.innerHTML = "Add Cart";
            }else{
                alert("Operation Cancelled");
            }
        }
    }

    //Remove Item from Cart
    function removeAPIFromCart(itemID, userID){
        var cartDetailID = itemID + "-" + userID;
        var cookieName = window.location.hostname.replaceAll(".","_").replaceAll(":","_") + "_" + userID + "_cart_items";
        var getAllCookieReturnVals = getCookieDet(cookieName);
        var explodeCookieReturnVals = getAllCookieReturnVals.split(" ");
        
        var checkItemExists = explodeCookieReturnVals.indexOf(itemID);
        if(checkItemExists !== -1){
            if(confirm("Do you want to remove this item from your cart?")){
                removeCartItemToBox(cartDetailID);
                window.location.href = window.location.href;
            }else{
                alert("Operation Cancelled");
            }
        }else{
            alert("Item was not included in cart or has already been removed from cart");
            window.location.href = window.location.href;
        }
    }

    function addCartItemToBox(cartDetailID){
        var expCartDetail = cartDetailID.split("-");
        var cartItemID = expCartDetail[0].trim();
        var cartUserID = expCartDetail[1].trim();
        
        var cookieName = window.location.hostname.replaceAll(".","_").replaceAll(":","_") + "_" + cartUserID + "_cart_items";
                if(getCookieDet(cookieName) !== false){
                    var getAllCookieReturnVals = getCookieDet(cookieName);
                    var explodeCookieReturnVals = getAllCookieReturnVals.split(" ");
                    if(explodeCookieReturnVals.indexOf(cartItemID) == -1){
                        document.cookie = cookieName + "=" + cartItemID + " " + getCookieDet(cookieName).trim() + ";";
                    }
                }else{
                    document.cookie = cookieName + "=" + cartItemID + ";";
                }
    }

    function removeCartItemToBox(cartDetailID){
        var expCartDetail = cartDetailID.split("-");
        var cartItemID = expCartDetail[0].trim();
        var cartUserID = expCartDetail[1].trim();
        var cookieName = window.location.hostname.replaceAll(".","_").replaceAll(":","_") + "_" + cartUserID + "_cart_items";
                if(getCookieDet(cookieName) !== false){
                    var getAllCookieReturnVals = getCookieDet(cookieName);
                    var explodeCookieReturnVals = getAllCookieReturnVals.split(" ");
                    if(explodeCookieReturnVals.indexOf(cartItemID) !== -1){
                        var cartItemSaved = "";
                        for(i=0; i < explodeCookieReturnVals.length; i++){
                            if(explodeCookieReturnVals[i].trim() !== cartItemID){
                                cartItemSaved += explodeCookieReturnVals[i].trim() + " ";
                            }
                        }
                        document.cookie = cookieName + "=" + cartItemSaved.trim() + ";";
                        
                    }
                }else{
                	document.cookie = cookieName + "=;";
                }
    }
    
    
    //Populate Product Price
    function getCSVDetails(columnCount){
        const csvFile = document.getElementById("csv-chooser");
        const selectedCSVFile = csvFile.files[0];
        
        if(selectedCSVFile == null){
            alert("No File Selected");
        }else{
            const selectedFileType = selectedCSVFile.type;
            if(selectedFileType == "text/csv"){
                const fileReader = new FileReader();
                fileReader.onload = function(event){
                    const fileContent = event.target.result;
                    const splitFileContent = fileContent.trim().split("\n");
                    const getCSVHeader = splitFileContent[0];
                    const splitCSVHeader = getCSVHeader.split(",");
                    if(splitCSVHeader.length == columnCount){
                        const columnArray = [];
                        const columnIndex = [];
                        const columnDuplicate = [];
                        for(let x = 0; x < splitCSVHeader.length; x++){
                        	if(!columnArray.includes(splitCSVHeader[x].trim())){
                        		columnArray.push(splitCSVHeader[x].trim());
                        		columnIndex.push(x);
                        	}else{
                        		columnDuplicate.push(splitCSVHeader[x])
                        	}
                        }
                        if(columnArray.length == columnCount){
                        	const getProductCodePosition = columnArray.indexOf("product_name");
                        	if(getProductCodePosition != "-1"){
                        		for(let x = 0; splitFileContent.length; x++){
                        			if(splitFileContent[x] != splitFileContent[0]){
                        				if(splitFileContent[x].trim() != ""){
                        					if(splitFileContent[x] != undefined){
                        						const splitCSVBodyVal = splitFileContent[x].split(",");
                        						if(splitCSVBodyVal.length == columnCount){
                        							const getProductCode = splitCSVBodyVal[getProductCodePosition];
                        							for(let x = 0; x < splitCSVBodyVal.length; x++){
                        								if(splitCSVBodyVal[x] !== getProductCode){
                        									const inputColumnBoxId = (getProductCode + "_" + columnArray[x]).toLowerCase();
                        									const getInputColumnBoxId = document.getElementById(inputColumnBoxId);
                        									
                        									if((getInputColumnBoxId != null) || (getInputColumnBoxId != undefined)){
                        										getInputColumnBoxId.value = Number(splitCSVBodyVal[x]);
                        									}
                        								}
                        							}
                        						}else{
                        						
                        						}
                        					}
                        				}
                        			}
                        		}
                        	}else{
                        		alert("Product Name Column Missing!");
                        	}
                        }else{
                        	alert("Duplicated Column: "+columnDuplicate.join(", "));
                        }
                    }else{
    											alert("Invalid Column: "+columnCount+" Column Needed");
                    }
                }
                fileReader.readAsText(selectedCSVFile);
            }else{
                alert("File Type Must Be CSV");
            }
        }
    }
    
    function downloadFile(text, filename) {
    	const element = document.createElement('a');
    	element.href = `data:text/csv;charset=utf-8,${encodeURIComponent(text)}`;
    	element.downloadt = filename;
    	element.click();
    }