	
	/*const selectPasswordInput = document.querySelectorAll('input[type="password"]');
    selectPasswordInput.forEach(element => {
        var newImg = document.createElement("img");
        newImg.src = "/asset/eye-show.svg";
        newImg.style = "width: auto; height: "+((element.clientHeight*35)/100)+"px; cursor: pointer; user-select: auto; position: absolute; margin: 0.98rem 0 0 -2rem;";
        newImg.addEventListener("click", () => {
            newSiblingElement = newImg.previousElementSibling;
            if(newSiblingElement.type == "password"){
                newSiblingElement.type = "text";
                newImg.src = "/asset/eye-hide.svg";
            }else{
                if(newSiblingElement.type == "text"){
                    newSiblingElement.type = "password";
                    newImg.src = "/asset/eye-show.svg";
                }
            }
        });
        element.parentNode.insertBefore(newImg, element.nextSibling);
    });*/

    function passwordToggle(toggledElement){
        alert(toggledElement.type );
    }
    setInterval(function(){
		var screenAngleArr = [90, 270];
		var screenOrientationAngle = screen.orientation.angle;
		if(screenAngleArr.indexOf(screenOrientationAngle) !== -1){
			//document.body.style = "max-width: 50%; min-height: 100%;";	
		}else{
			//document.body.style = "width: 100%;";
		}
	}, 1000);
	
	function askPermissionSubBtn(elementProperty, dialogText){
		if(dialogText.trim().length >= 1){
			dialogWord = dialogText;
		}else{
			dialogWord = "Are you sure you want to continue?";
		}
		
		if(confirm(dialogWord)){
			elementProperty.type = "submit";
		}else{
			alert("Operation Cancelled");
		}
	}
	
	function copyText(copyResponseText, textValue){
		if(textValue.trim().length >= 1){
			var copyResponse;
			if(copyResponseText.trim().length >= 1){
				copyResponse = copyResponseText;
			}else{
				copyResponse = "Content copied to clipboard";
			}
			
			const copyTextToBoard = async () => {
			try {
				await navigator.clipboard.writeText(textValue);
				alert(copyResponse);
			} catch (err) {
					alert('Failed to copy: '+ err);
				}
			}
			copyTextToBoard();
		}else{
			alert("No text to copy");
		}
	}
	
    function tickAirtimeCarrier(networkName){
        var carrierMTN = ["803","702","703","704","903","806","706","707","813","810","814","816","906","916","913","903"];
        var carrierAirtel = ["701","708","802","808","812","901","902","904","907","911","912"];
        var carrierGlo = ["805","705","905","807","815","811","915"];
        var carrier9mobile = ["809","817","818","908","909"];
        var allNetwork = [];
        allNetwork = allNetwork.concat(carrierMTN);
        allNetwork = allNetwork.concat(carrierAirtel);
        allNetwork = allNetwork.concat(carrierGlo);
        allNetwork = allNetwork.concat(carrier9mobile);

        var amount = document.getElementById("product-amount");
        var phoneNo = document.getElementById("phone-number");
        var phoneByPass = document.getElementById("phone-bypass");
        var isprovider = document.getElementById("isprovider");

        if(phoneByPass.checked === false){
            if(allNetwork.indexOf(phoneNo.value.substring(1,4)) !== -1){
                if(carrierAirtel.indexOf(phoneNo.value.substring(1,4)) !== -1){
                    carrierAirtimeInfo("airtel",amount.value,phoneNo.value);
                }
                if(carrierMTN.indexOf(phoneNo.value.substring(1,4)) !== -1){
                    carrierAirtimeInfo("mtn",amount.value,phoneNo.value);
                }
                if(carrierGlo.indexOf(phoneNo.value.substring(1,4)) !== -1){
                    carrierAirtimeInfo("glo",amount.value,phoneNo.value);
                }
                if(carrier9mobile.indexOf(phoneNo.value.substring(1,4)) !== -1){
                    carrierAirtimeInfo("9mobile",amount.value,phoneNo.value);
                }
            }else{
                carrierAirtimeInfo("","","");
            }
        }else{
            isprovider.value = networkName;
            carrierAirtimeInfo(networkName,amount.value,phoneNo.value);
        }
    }

    function carrierAirtimeInfo(ispName,productAmount,phoneNo){
        var ispNames = ["airtel","mtn","glo","9mobile"];
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("isprovider");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
            	if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
            	if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.png";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }
        
        if(Number(phoneNo) && (phoneNo.length === 11) && Number(productAmount) && (productAmount.length >= 3) && (productAmount >= 1) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (productStatus === "enabled")){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }
	
    //Bulk Airtime Info
    function tickBulkAirtimeCarrier(networkName){
        var carrierMTN = ["803","702","703","704","903","806","706","707","813","810","814","816","906","916","913","903"];
        var carrierAirtel = ["701","708","802","808","812","901","902","904","907","911","912"];
        var carrierGlo = ["805","705","905","807","815","811","915"];
        var carrier9mobile = ["809","817","818","908","909"];
        var allNetwork = [];
        allNetwork = allNetwork.concat(carrierMTN);
        allNetwork = allNetwork.concat(carrierAirtel);
        allNetwork = allNetwork.concat(carrierGlo);
        allNetwork = allNetwork.concat(carrier9mobile);

        var amount = document.getElementById("product-amount");
        var phoneNo = document.getElementById("phone-number");
        var filteredPhoneNo = document.getElementById("filtered-phone-number");
        
        //Multiple Numbers
        //Split Numbers 
        var splitPhoneNumbers = phoneNo.value.replaceAll("\n", ",").replaceAll(" ", "").split(",");
    
        //Filter Numbers
        var filteredNumbers = splitPhoneNumbers.filter(phone => Number(phone) && phone.trim().length === 11);
        filteredNumbers = [... new Set(filteredNumbers)];
        //Update Filtered Phone Numbers
        filteredPhoneNo.value = filteredNumbers.join(",");
        
        document.getElementById("phone-numbers-span").innerHTML = "Phone Number Count: " + filteredNumbers.length;

        var phoneByPass = document.getElementById("phone-bypass");
        var isprovider = document.getElementById("isprovider");
        
        isprovider.value = "";
        var ispNames = ["airtel","mtn","glo","9mobile"];
        for(let x = 0; x < ispNames.length; x++){
            var ispImage = document.getElementById(ispNames[x]+"-lg");
            ispImage.src = "/asset/"+ispNames[x]+".png";
            ispImage.classList.remove("br-radius-5px");
            ispImage.classList.add("br-radius-100px");
            ispImage.style = "filter: grayscale(0%);";
        }

        if(phoneByPass.checked === false){
            var phoneNetworkArr = [];
            var filterFirstFourNumbers = filteredNumbers.map(phone => phone.trim().substring(1,4));
            
            if(allNetwork.includes(filterFirstFourNumbers) !== -1){
                if(carrierAirtel.some(phone => filterFirstFourNumbers.indexOf(phone) !== -1) == true){
                    if(phoneNetworkArr.indexOf("airtel") == -1){
                        phoneNetworkArr.push("airtel");
                    }
                    carrierBulkAirtimeInfo(phoneNetworkArr, "airtel",amount.value);
                }
                if(carrierMTN.some(phone => filterFirstFourNumbers.indexOf(phone) !== -1) == true){
                    if(phoneNetworkArr.indexOf("mtn") == -1){
                        phoneNetworkArr.push("mtn");
                    }
                    carrierBulkAirtimeInfo(phoneNetworkArr, "mtn",amount.value);
                }
                if(carrierGlo.some(phone => filterFirstFourNumbers.indexOf(phone) !== -1) == true){
                    if(phoneNetworkArr.indexOf("glo") == -1){
                        phoneNetworkArr.push("glo");
                    }
                    carrierBulkAirtimeInfo(phoneNetworkArr, "glo",amount.value);
                }
                if(carrier9mobile.some(phone => filterFirstFourNumbers.indexOf(phone) !== -1) == true){
                    if(phoneNetworkArr.indexOf("9mobile") == -1){
                        phoneNetworkArr.push("9mobile");
                    }
                    carrierBulkAirtimeInfo(phoneNetworkArr, "9mobile",amount.value);
                }
            }else{
                carrierBulkAirtimeInfo([""],"","");
            }
            console.log(phoneNetworkArr);
        }else{
            isprovider.value = networkName;
            carrierBulkAirtimeInfo([networkName], networkName,amount.value);
        }
    }

    function carrierBulkAirtimeInfo(ispNetworkArr, ispName,productAmount){
        var ispNames = ["airtel","mtn","glo","9mobile"];
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("isprovider");
        var productStatus;

        console.log(ispNetworkArr);
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            notTickedService.classList.remove("br-radius-5px");
            notTickedService.classList.add("br-radius-100px");
            if(ispNames.some(network => ispNetworkArr.indexOf(network)) == false){
            	notTickedService.src = "/asset/"+ispNames[x]+".png";
            }else{
            	if(ispName.trim().length >= 1){
                    notTickedService.src = "/asset/"+ispNames[x]+".png";
                }else{
                    notTickedService.src = "/asset/"+ispNames[x]+".png";
                }
               	isprovider.value = ispNetworkArr.join(", ");
                notTickedService.style = "filter: grayscale(100%);";
            }

            var splitProviderName = isprovider.value.trim().split(",");
            splitProviderName = splitProviderName.map(provider => provider.trim().toLowerCase());
            
            for(let x = 0; x < splitProviderName.length; x++){
                var ispImage = document.getElementById(splitProviderName[x]+"-lg");
                ispImage.src = "/asset/"+splitProviderName[x]+"-marked.png";
                if(ispImage.getAttribute("product-status") != "enabled"){
                    productStatus = "disabled";
                    ispImage.style = "filter: grayscale(100%);";
                    ispImage.classList.remove("br-radius-5px");
                    ispImage.classList.add("br-radius-100px");
                    document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
                }else{
                    productStatus = "enabled";
                    ispImage.style = "filter: grayscale(0%);";
                    ispImage.classList.remove("br-radius-100px");
                    ispImage.classList.add("br-radius-5px");
                    document.getElementById("product-status-span").innerHTML = "";
                }
            }
        }

        
        
        if((ispNames.some(isp => ispNetworkArr.indexOf(isp)) == true) && Number(productAmount) && (productAmount.length >= 3) && (productAmount >= 1) && (isprovider.value.length >= 1) && (ispNames.includes(isprovider.value) !== -1) && (productStatus === "enabled")){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }
	
    function tickDataCarrier(networkName){
        var carrierMTN = ["803","702","703","704","903","806","706","707","813","810","814","816","906","916","913","903"];
        var carrierAirtel = ["701","708","802","808","812","901","902","904","907","911","912"];
        var carrierGlo = ["805","705","905","807","815","811","915"];
        var carrier9mobile = ["809","817","818","908","909"];
        var allNetwork = [];
        allNetwork = allNetwork.concat(carrierMTN);
        allNetwork = allNetwork.concat(carrierAirtel);
        allNetwork = allNetwork.concat(carrierGlo);
        allNetwork = allNetwork.concat(carrier9mobile);

        var amount = document.getElementById("product-amount");
        var phoneNo = document.getElementById("phone-number");
        var phoneByPass = document.getElementById("phone-bypass");
        var isprovider = document.getElementById("isprovider");

        if(phoneByPass.checked === false){
            if(allNetwork.indexOf(phoneNo.value.substring(1,4)) !== -1){
                if(carrierAirtel.indexOf(phoneNo.value.substring(1,4)) !== -1){
                    carrierDataInfo("airtel",amount.value,phoneNo.value);
                }
                if(carrierMTN.indexOf(phoneNo.value.substring(1,4)) !== -1){
                    carrierDataInfo("mtn",amount.value,phoneNo.value);
                }
                if(carrierGlo.indexOf(phoneNo.value.substring(1,4)) !== -1){
                    carrierDataInfo("glo",amount.value,phoneNo.value);
                }
                if(carrier9mobile.indexOf(phoneNo.value.substring(1,4)) !== -1){
                    carrierDataInfo("9mobile",amount.value,phoneNo.value);
                }
            }else{
                carrierDataInfo("","","");
            }
        }else{
            isprovider.value = networkName;
            carrierDataInfo(networkName,amount.value,phoneNo.value);
        }
    }

    function carrierDataInfo(ispName,productAmount,phoneNo){
        var ispNames = ["airtel","mtn","glo","9mobile"];
        var dataTypeArray = {"shared-data":"shared-data", "sme-data":"sme-data","cg-data":"cg-data","dd-data":"dd-data"};
        var internetDataType = document.getElementById("internet-data-type");
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("isprovider");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
                if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
                if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.png";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        if(ispName.length >= 1){
            for(x=0; x < amount.options.length; x++){
                if(amount.options[x].value.trim() !== ""){
                    if(amount.options[x].getAttribute("product-category") == isprovider.value+"-"+dataTypeArray[internetDataType.value]){
                        amount.options[x].hidden = false;
                    }else{
                        amount.options[x].hidden = true;
                    }
                }
            }
        }
        if(Number(phoneNo) && (phoneNo.length === 11) && (productAmount.length >= 1) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (dataTypeArray[internetDataType.value].trim() !== "") && (productStatus === "enabled")){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }
    
    
    //Bulk Data Info
    function tickBulkDataCarrier(networkName){
        var carrierMTN = ["803","702","703","704","903","806","706","707","813","810","814","816","906","916","913","903"];
        var carrierAirtel = ["701","708","802","808","812","901","902","904","907","911","912"];
        var carrierGlo = ["805","705","905","807","815","811","915"];
        var carrier9mobile = ["809","817","818","908","909"];
        var allNetwork = [];
        allNetwork = allNetwork.concat(carrierMTN);
        allNetwork = allNetwork.concat(carrierAirtel);
        allNetwork = allNetwork.concat(carrierGlo);
        allNetwork = allNetwork.concat(carrier9mobile);
    
        var amount = document.getElementById("product-amount");
        var phoneNo = document.getElementById("phone-number");
        var filteredPhoneNo = document.getElementById("filtered-phone-number");
        
        //Multiple Numbers
        //Split Numbers 
        var splitPhoneNumbers = phoneNo.value.replaceAll("\n", ",").replaceAll(" ", "").split(",");
    
        //Filter Numbers
        var filteredNumbers = splitPhoneNumbers.filter(phone => Number(phone) && phone.trim().length === 11);
        filteredNumbers = [... new Set(filteredNumbers)];
        //Update Filtered Phone Numbers
        filteredPhoneNo.value = filteredNumbers.join(",");
        
        document.getElementById("phone-numbers-span").innerHTML = "Phone Number Count: " + filteredNumbers.length;
    	
        var phoneByPass = document.getElementById("phone-bypass");
        var isprovider = document.getElementById("isprovider");
        
        isprovider.value = "";
        var ispNames = ["airtel","mtn","glo","9mobile"];
        for(let x = 0; x < ispNames.length; x++){
            var ispImage = document.getElementById(ispNames[x]+"-lg");
            ispImage.src = "/asset/"+ispNames[x]+".png";
            ispImage.classList.remove("br-radius-5px");
            ispImage.classList.add("br-radius-100px");
            ispImage.style = "filter: grayscale(0%);";
        }
    
        if(phoneByPass.checked === false){
            var phoneNetworkArr = [];
            var filterFirstFourNumbers = filteredNumbers.map(phone => phone.trim().substring(1,4));
            
            if(allNetwork.includes(filterFirstFourNumbers) !== -1){
                if(carrierAirtel.some(phone => filterFirstFourNumbers.indexOf(phone) !== -1) == true){
                    if(phoneNetworkArr.length == 0){
                        phoneNetworkArr.push("airtel");
                    	carrierBulkDataInfo(phoneNetworkArr, "airtel",amount.value);
                    }
                }
                if(carrierMTN.some(phone => filterFirstFourNumbers.indexOf(phone) !== -1) == true){
                    if(phoneNetworkArr.length == 0){
                        phoneNetworkArr.push("mtn");
                    	carrierBulkDataInfo(phoneNetworkArr, "mtn",amount.value);
                    }
                }
                if(carrierGlo.some(phone => filterFirstFourNumbers.indexOf(phone) !== -1) == true){
                    if(phoneNetworkArr.length == 0){
                        phoneNetworkArr.push("glo");
                    	carrierBulkDataInfo(phoneNetworkArr, "glo",amount.value);
                    }
                }
                if(carrier9mobile.some(phone => filterFirstFourNumbers.indexOf(phone) !== -1) == true){
                    if(phoneNetworkArr.length == 0){
                        phoneNetworkArr.push("9mobile");
                    	carrierBulkDataInfo(phoneNetworkArr, "9mobile",amount.value);
                    }
                }
            }else{
                carrierBulkDataInfo([""],"","");
            }
            console.log(phoneNetworkArr);
        }else{
            isprovider.value = networkName;
            carrierBulkDataInfo([networkName], networkName,amount.value);
        }
    }
    
    function carrierBulkDataInfo(ispNetworkArr, ispName,productAmount){
        var ispNames = ["airtel","mtn","glo","9mobile"];
        var dataTypeArray = {"shared-data":"shared-data", "sme-data":"sme-data","cg-data":"cg-data","dd-data":"dd-data"};
        var internetDataType = document.getElementById("internet-data-type");
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("isprovider");
        var productStatus;
    
        console.log(ispNetworkArr);
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            notTickedService.classList.remove("br-radius-5px");
            notTickedService.classList.add("br-radius-100px");
            if(ispNames.some(network => ispNetworkArr.indexOf(network)) == false){
            	notTickedService.src = "/asset/"+ispNames[x]+".png";
            }else{
            	if(ispName.trim().length >= 1){
                    notTickedService.src = "/asset/"+ispNames[x]+".png";
                }else{
                    notTickedService.src = "/asset/"+ispNames[x]+".png";
                }
               	isprovider.value = ispNetworkArr.join(", ");
                notTickedService.style = "filter: grayscale(100%);";
            }
    
            var splitProviderName = isprovider.value.trim().split(",");
            splitProviderName = splitProviderName.map(provider => provider.trim().toLowerCase());
            
            for(let x = 0; x < splitProviderName.length; x++){
                var ispImage = document.getElementById(splitProviderName[x]+"-lg");
                ispImage.src = "/asset/"+splitProviderName[x]+"-marked.png";
                if(ispImage.getAttribute("product-status") != "enabled"){
                    productStatus = "disabled";
                    ispImage.style = "filter: grayscale(100%);";
                    ispImage.classList.remove("br-radius-5px");
                    ispImage.classList.add("br-radius-100px");
                    document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
                }else{
                    productStatus = "enabled";
                    ispImage.style = "filter: grayscale(0%);";
                    ispImage.classList.remove("br-radius-100px");
                    ispImage.classList.add("br-radius-5px");
                    document.getElementById("product-status-span").innerHTML = "";
                }
            }
        }
    	
        if(ispName.length >= 1){
        	for(x=0; x < amount.options.length; x++){
        		if(amount.options[x].value.trim() !== ""){
        			if(amount.options[x].getAttribute("product-category") == isprovider.value+"-"+dataTypeArray[internetDataType.value]){
        				amount.options[x].hidden = false;
        			}else{
        				amount.options[x].hidden = true;
      				}
        		}
        	}
        }
        
        if((ispNames.some(isp => ispNetworkArr.indexOf(isp)) == true) && (productAmount.length >= 1) && (isprovider.value.length >= 1) && (ispNames.includes(isprovider.value) !== -1) && (dataTypeArray[internetDataType.value].trim() !== "") && (productStatus === "enabled")){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }

    function resetDataQuantity(){
        var amount = document.getElementById("product-amount");
        
        for(x=0; x < amount.options.length; x++){
            if(amount.options[x].value.trim() == ""){
                amount.options[x].hidden = true;
                amount.options[x].selected = true;
                amount.options[x].default = true;
            }
        }
    }

    function tickDataRechargeCarrier(networkName){
        var amount = document.getElementById("product-amount");
        var qty = document.getElementById("quantity");
        var isprovider = document.getElementById("isprovider");
        if(!networkName){
            carrierDataRechargeInfo(isprovider.value,amount.value,qty.value);
        }else{
            isprovider.value = networkName;
            carrierDataRechargeInfo(networkName,amount.value,qty.value);
        }

    }

    function carrierDataRechargeInfo(ispName,productAmount,qty){
        var ispNames = ["airtel","mtn","glo","9mobile"];
        var dataTypeArray = {"datacard":"datacard","rechargecard":"rechargecard"};
        var internetDataType = document.getElementById("internet-data-type");
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("isprovider");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
                if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
                if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.png";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        if(ispName.length >= 1){
            for(x=0; x < amount.options.length; x++){
                if(amount.options[x].value.trim() !== ""){
                    if(amount.options[x].getAttribute("product-category") == isprovider.value+"-"+dataTypeArray[internetDataType.value]){
                        amount.options[x].hidden = false;
                    }else{
                        amount.options[x].hidden = true;
                    }
                }
            }
        }
        if(Number(qty) && (qty.length >= 1) && (qty >= 1) && (productAmount.length >= 1) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (dataTypeArray[internetDataType.value].trim() !== "") && (productStatus === "enabled")){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }

    function resetDataRechargeQuantity(){
        var amount = document.getElementById("product-amount");
        
        for(x=0; x < amount.options.length; x++){
            if(amount.options[x].value.trim() == ""){
                amount.options[x].hidden = true;
                amount.options[x].selected = true;
                amount.options[x].default = true;
            }
        }
    }

    function tickVirtualCardRechargeCarrier(networkName){
        var amount = document.getElementById("product-amount");
        var qty = document.getElementById("quantity");
        var isprovider = document.getElementById("isprovider");
        if(!networkName){
            carrierVirtualCardRechargeInfo(isprovider.value,amount.value,qty.value);
        }else{
            isprovider.value = networkName;
            carrierVirtualCardRechargeInfo(networkName,amount.value,qty.value);
        }

    }

    function carrierVirtualCardRechargeInfo(ispName,productAmount,qty){
        var ispNames = ["mastercard","visa","verve"];
        var dataTypeArray = {"nairacard":"nairacard","dollarcard":"dollarcard"};
        var internetVirtualCardType = document.getElementById("internet-data-type");
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("isprovider");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
                if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
                if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.png";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        if(ispName.length >= 1){
            for(x=0; x < amount.options.length; x++){
                if(amount.options[x].value.trim() !== ""){
                    if(amount.options[x].getAttribute("product-category") == isprovider.value+"-"+dataTypeArray[internetVirtualCardType.value]){
                        amount.options[x].hidden = false;
                    }else{
                        amount.options[x].hidden = true;
                    }
                }
            }
        }
        if(Number(qty) && (qty.length >= 1) && (qty >= 1) && (productAmount.length >= 1) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (dataTypeArray[internetVirtualCardType.value].trim() !== "") && (productStatus === "enabled")){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }

    function resetVirtualCardRechargeQuantity(){
        var amount = document.getElementById("product-amount");
        
        for(x=0; x < amount.options.length; x++){
            if(amount.options[x].value.trim() == ""){
                amount.options[x].hidden = true;
                amount.options[x].selected = true;
                amount.options[x].default = true;
            }
        }
    }
    function tickCableCarrier(networkName){
        var carrierStartimes = ["0"];
        var carrierDstv = ["8"];
        var carrierGotv = ["7"];
        var carrierShowmax = ["9"];
        var allNetwork = [];
        allNetwork = allNetwork.concat(carrierStartimes);
        allNetwork = allNetwork.concat(carrierDstv);
        allNetwork = allNetwork.concat(carrierGotv);
        allNetwork = allNetwork.concat(carrierShowmax);

        var amount = document.getElementById("product-amount");
        var iucNo = document.getElementById("iuc-number");
        var isprovider = document.getElementById("isprovider");
    	
		if((networkName == undefined) || (networkName.trim().length < 1)){
			if(isprovider.value.trim().length > 0){
				const networkArr = ["startimes","dstv","gotv", "showmax"];
				if(networkArr.indexOf(isprovider.value) != "-1"){
					carrierCableInfo(isprovider.value,amount.value,iucNo.value);
				}else{
					carrierCableInfo("","","");
				}
			}else{
				carrierCableInfo("","","");
			}
		}else{
        	const networkArr = ["startimes","dstv","gotv", "showmax"];
        	if(networkArr.indexOf(networkName) != "-1"){
        		carrierCableInfo(networkName,amount.value,iucNo.value);
        	}else{
        		carrierCableInfo("","","");
        	}
        }
    }

    function carrierCableInfo(ispName,productAmount,iucNo){
        var ispNames = ["startimes","dstv","gotv","showmax"];
        var cableTypeArray = {"startimes-cable":"startimes-cable","dstv-cable":"dstv-cable","gotv-cable":"gotv-cable","showmax-cable":"showmax-cable"};
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("isprovider");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
                if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".jpg";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".jpg";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
                if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.jpg";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        if(ispName.length >= 1){
            for(x=0; x < amount.options.length; x++){
                if(amount.options[x].value.trim() !== ""){
                    if(amount.options[x].getAttribute("product-category") == isprovider.value+"-cable"){
                        amount.options[x].hidden = false;
                    }else{
                        amount.options[x].hidden = true;
                    }
                }
            }
        }

        if(Number(iucNo) && (iucNo.length >= 10) && (productAmount.length >= 1) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (productStatus === "enabled")){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }

    function resetCableQuantity(){
        var amount = document.getElementById("product-amount");
        
        for(x=0; x < amount.options.length; x++){
            if(amount.options[x].value.trim() == ""){
                amount.options[x].hidden = true;
                amount.options[x].selected = true;
                amount.options[x].default = true;
            }
        }
    }
    
    function tickExamCarrier(networkName){
        var amount = document.getElementById("product-amount");
        carrierExamInfo(networkName,amount.value,"");
    }

    function carrierExamInfo(ispName,productAmount,emptyInfo){
        var ispNames = ["waec","neco","nabteb","jamb"];
        var cableTypeArray = {"waec-exam":"waec-exam","neco-exam":"neco-exam","nabteb-exam":"nabteb-exam","jamb-exam":"jamb-exam"};
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("examname");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
                if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".jpg";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".jpg";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
                if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.jpg";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        if(ispName.length >= 1){
            for(x=0; x < amount.options.length; x++){
                if(amount.options[x].value.trim() !== ""){
                    if(amount.options[x].getAttribute("product-category") == isprovider.value+"-exam"){
                        amount.options[x].hidden = false;
                    }else{
                        amount.options[x].hidden = true;
                    }
                }
            }
        }
        pickExamQty();
    }

    function pickExamQty(){
        var ispNames = ["waec","neco","nabteb","jamb"];
        var cableTypeArray = {"waec-exam":"waec-exam","neco-exam":"neco-exam","nabteb-exam":"nabteb-exam","jamb-exam":"jamb-exam"};
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("examname");
        var ispName = isprovider.value;

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.jpg";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        setInterval(function(){
            if((amount.value.length >= 1) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (productStatus === "enabled")){
                proceedBtn.style = "pointer-events: auto;";
            }else{
                proceedBtn.style = "pointer-events: none;";
            }
        }, 1000);
    }

    function resetExamQuantity(){
        var amount = document.getElementById("product-amount");
        
        for(x=0; x < amount.options.length; x++){
            if(amount.options[x].value.trim() == ""){
                amount.options[x].hidden = true;
                amount.options[x].selected = true;
                amount.options[x].default = true;
            }
        }
    }
    

    function tickElectricCarrier(networkName){
        var amount = document.getElementById("product-amount");
        carrierElectricInfo(networkName,amount.value,"");
    }

    function carrierElectricInfo(ispName,productAmount,emptyInfo){
        var ispNames = ["ekedc","eedc","ikedc","jedc","kedco","ibedc","phed","aedc","yedc","bedc","kaedco","aba"];
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("electricname");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
                if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".jpg";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".jpg";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
                if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.jpg";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        if(ispName.length >= 1){
            for(x=0; x < amount.options.length; x++){
                if(amount.options[x].value.trim() !== ""){
                    if(amount.options[x].getAttribute("product-category") == isprovider.value+"-exam"){
                        amount.options[x].hidden = false;
                    }else{
                        amount.options[x].hidden = true;
                    }
                }
            }
        }
        pickElectricQty();
    }

    function pickElectricQty(){
        var ispNames = ["ekedc","eedc","ikedc","jedc","kedco","ibedc","phed","aedc","yedc","bedc","kaedco","aba"];
        var meterNoArr = ["prepaid", "postpaid"];
        var amount = document.getElementById("product-amount");
        var meter_type = document.getElementById("meter-type");
        var meter_number = document.getElementById("meter-number");

        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("electricname");
        var ispName = isprovider.value;

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.jpg";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        setInterval(function(){
            if((meterNoArr.indexOf(meter_type.value) !== -1) && Number(meter_number.value) && (meter_number.value.length >= 10) && Number(amount.value) && (amount.value >= 100) && (amount.value.length >= 3) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (productStatus === "enabled")){
                proceedBtn.style = "pointer-events: auto;";
            }else{
                proceedBtn.style = "pointer-events: none;";
            }
        }, 1000);
    }

    function resetElectricQuantity(){
        var amount = document.getElementById("product-amount");
        amount.value = "";
    }
    


    function tickBettingCarrier(networkName){
        var amount = document.getElementById("product-amount");
        carrierBettingInfo(networkName,amount.value,"");
    }

    function carrierBettingInfo(ispName,productAmount,emptyInfo){
        var ispNames = ["msport", "naijabet", "nairabet", "bet9ja-agent", "betland", "betlion", "supabet", "bet9ja", "bangbet", "betking", "1xbet", "betway", "merrybet", "mlotto", "western-lotto", "hallabet", "green-lotto"];
        var amount = document.getElementById("product-amount");
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("bettingname");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
                if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".jpg";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".jpg";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
                if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.jpg";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        if(ispName.length >= 1){
            for(x=0; x < amount.options.length; x++){
                if(amount.options[x].value.trim() !== ""){
                    if(amount.options[x].getAttribute("product-category") == isprovider.value+"-exam"){
                        amount.options[x].hidden = false;
                    }else{
                        amount.options[x].hidden = true;
                    }
                }
            }
        }
        pickBettingQty();
    }

    function pickBettingQty(){
        var ispNames = ["msport", "naijabet", "nairabet", "bet9ja-agent", "betland", "betlion", "supabet", "bet9ja", "bangbet", "betking", "1xbet", "betway", "merrybet", "mlotto", "western-lotto", "hallabet", "green-lotto"];
        var customerNoArr = ["prepaid", "postpaid"];
        var amount = document.getElementById("product-amount");
        var customer_id = document.getElementById("customer-id");

        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("bettingname");
        var ispName = isprovider.value;

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.jpg";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }

        setInterval(function(){
            if(Number(customer_id.value) && (customer_id.value.length >= 10) && Number(amount.value) && (amount.value >= 100) && (amount.value.length >= 3) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (productStatus === "enabled")){
                proceedBtn.style = "pointer-events: auto;";
            }else{
                proceedBtn.style = "pointer-events: none;";
            }
        }, 1000);
    }

    function resetBettingQuantity(){
        var amount = document.getElementById("product-amount");
        amount.value = "";
    }
    


    function confirmUser(){
        var username = document.getElementById("share-fund-user").value;
        
        var userStatus = document.getElementById("user-status-span");
        var selectUserHttp = new XMLHttpRequest();
        selectUserHttp.open("POST", "../select-user.php");
        selectUserHttp.setRequestHeader("Content-Type", "application/json");
        var selectUserHttpBody = JSON.stringify({user: username});
        selectUserHttp.onload = function(){
            if((selectUserHttp.readyState === 4) && (selectUserHttp.status === 200)){
                var jsonDecoded = JSON.parse(selectUserHttp.responseText);
                if(username.trim() !== ""){
                    if(jsonDecoded.status == 200){
                        userStatus.innerHTML = jsonDecoded.text;
						userVerification(true);
                    }else{
                        userStatus.innerHTML = jsonDecoded.text;
                        userVerification(false);
                    }
                }else{
                    userStatus.innerHTML = "Enter User ID";
                    userVerification(false);
                }
            }else{
                userStatus.innerHTML = "System Error: Cannot Verify User";
                userVerification(false);
            }
        }
        selectUserHttp.send(selectUserHttpBody);
    }
    
    function uPCheckoutRef(){
        var checkoutRef = document.getElementById("num-ref");
       	if(checkoutRef.value.length < 1){
        var selectUserHttp = new XMLHttpRequest();
        selectUserHttp.open("GET", "../random-upaid.php");
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

    function userVerification(userAccountStatus){
    	var amount = document.getElementById("share-fund-amount").value.trim();
    	var proceedBtn = document.getElementById("proceedBtn");
    	
    	if((userAccountStatus == true) && Number(amount) && (amount.length >= 2) && (amount >= 10) && (amount <= 99999)){
    		proceedBtn.style = "pointer-events: auto;";
    	}else{
    		proceedBtn.style = "pointer-events: none;";
    	}
    }

    function submitPayment(elementTag){
        var elementTag = elementTag.value;
        var proceedBtn = document.getElementById("proceedBtn");

        if(Number(elementTag) && (elementTag.length >= 2) && (elementTag >= 10) && (elementTag <= 999999)){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }
    
    document.getElementById("proceedBtn").onclick = function(){
        var proceedBtn = document.getElementById("proceedBtn");
        proceedBtn.type = "submit";
    }
    
    function toggleSlider(){
    	var headerSliderDiv = document.getElementById("headerSliderDiv");
    	var toggleSlider = document.getElementById("toggleSlider");
    	var bodyDiv = document.getElementById("bodyDiv");
    	var bodyOpacityDiv = document.getElementById("bodyOpacityDiv");
    	var footerMenuDiv = document.getElementById("footerMenuDiv");
    
        if(headerSliderDiv.classList.contains("m-width-0")){
            headerSliderDiv.classList.remove("m-width-0");
            headerSliderDiv.classList.add("m-width-40");
            headerSliderDiv.style.transition = "width 0.2s linear 0.2s";
            toggleSlider.src = "/asset/close-black.png";
            bodyDiv.style.pointerEvents = "none";
            bodyDiv.classList.remove("m-z-index-1");
            bodyDiv.classList.add("m-z-index-0");
            footerMenuDiv.style.display = "none";
            bodyOpacityDiv.classList.remove("m-z-index-0");
            bodyOpacityDiv.classList.add("m-z-index-1");
            bodyOpacityDiv.style = "background: black;";
            bodyOpacityDiv.style.height = (document.body.offsetHeight - 70)+"px";
            bodyOpacityDiv.style.opacity = "0.5";
        }else{
            headerSliderDiv.classList.remove("m-width-40");
            headerSliderDiv.classList.add("m-width-0");
            headerSliderDiv.style.transition = "width 0.2s linear 0.2s";
            toggleSlider.src = "/asset/open-black.png";
            bodyDiv.style.pointerEvents = "auto";
            bodyDiv.classList.remove("m-z-index-0");
            bodyDiv.classList.add("m-z-index-1");
            bodyOpacityDiv.classList.remove("m-z-index-1");
            bodyOpacityDiv.classList.add("m-z-index-0");
            bodyOpacityDiv.style = "background: transparent;";
            bodyOpacityDiv.style.height = "0px";
            bodyOpacityDiv.style.opacity = "1";
            
            setTimeout(function(){
            	footerMenuDiv.style.display= "inline-block";
            }, 1000);
        }
    }

    function tickPaymentGateway(getElement, networkName, productID, buttonID, fileExt){
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
            checkPaymentGatewayDetails(buttonID,"1");
        }
    }

    function checkPaymentGatewayDetails(buttonID,funID){
        var fundAmount = document.getElementById("fund-amount");
        var productName = document.getElementById("gatewayname");
        var installProduct = document.getElementById(buttonID);
        var productStatus = document.getElementById("product-status-span");
        uPCheckoutRef();
        
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

	setInterval(() => {
		filterBulkSMSPhoneNumbers();
	});
	
    function filterBulkSMSPhoneNumbers(){
        var rawPhoneNos = document.getElementById("phone-numbers");
        var filteredPhoneNos = document.getElementById("filtered-phone-numbers");
        var phoneNoCountSpan = document.getElementById("phone-numbers-span");
        var phoneByPass = document.getElementById("phone-bypass");
        var isprovider = document.getElementById("isprovider");

        var carrierMTN = ["803","702","703","704","903","806","706","707","813","810","814","816","906","916","913","903"];
        var carrierAirtel = ["701","708","802","808","812","901","902","904","907","911","912"];
        var carrierGlo = ["805","705","905","807","815","811","915"];
        var carrier9mobile = ["809","817","818","908","909"];
        var allNetwork = [];
        allNetwork = allNetwork.concat(carrierMTN);
        allNetwork = allNetwork.concat(carrierAirtel);
        allNetwork = allNetwork.concat(carrierGlo);
        allNetwork = allNetwork.concat(carrier9mobile);
		var allAvailableNetwork = ["mtn","airtel","glo","9mobile"];
        
        var splitRawPhoneNos = rawPhoneNos.value.replaceAll("\n",",").replaceAll(/[^\d,]/g,"").split(",");
        var uniquePhoneNos = [];
        for(i=0; i < splitRawPhoneNos.length; i++){
            if(splitRawPhoneNos[i].length === 11){
                if(uniquePhoneNos.indexOf(splitRawPhoneNos[i]) == -1){
                    if(phoneByPass.checked === true){
                        if(allNetwork.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                            if((isprovider.value.trim() == "") || (allAvailableNetwork.indexOf(isprovider.value) == -1)){
                                if(carrierMTN.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                                	isprovider.value = "mtn";
                                	var selectedNetworkName = isprovider.value.trim();
                                	tickBulkSMSCarrier(selectedNetworkName);
                                    uniquePhoneNos.push(splitRawPhoneNos[i]);
                                }
                                
                                if(carrierAirtel.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                                	isprovider.value = "airtel";
                                	var selectedNetworkName = isprovider.value.trim();
                                	tickBulkSMSCarrier(selectedNetworkName);
                                	uniquePhoneNos.push(splitRawPhoneNos[i]);
                                }
                                
                                if(carrierGlo.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                                	isprovider.value = "glo";
                                	var selectedNetworkName = isprovider.value.trim();
                                	tickBulkSMSCarrier(selectedNetworkName);
                                	uniquePhoneNos.push(splitRawPhoneNos[i]);
                                }
                                
                                if(carrier9mobile.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                                	isprovider.value = "9mobile";
                                	var selectedNetworkName = isprovider.value.trim();
                                	tickBulkSMSCarrier(selectedNetworkName);
                                	uniquePhoneNos.push(splitRawPhoneNos[i]);
                                }
                            }else{
                                if(carrierMTN.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                                	uniquePhoneNos.push(splitRawPhoneNos[i]);
                                }
                                
                                if(carrierAirtel.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                                	uniquePhoneNos.push(splitRawPhoneNos[i]);
                                }
                                
                                if(carrierGlo.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                                	uniquePhoneNos.push(splitRawPhoneNos[i]);
                                }
                                
                                if(carrier9mobile.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                                	uniquePhoneNos.push(splitRawPhoneNos[i]);
                                }
                            }
                        }
                    }else{
                    	if(allNetwork.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                    		if((isprovider.value.trim() == "") || (allAvailableNetwork.indexOf(isprovider.value) == -1)){
                    			if(carrierMTN.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                    				isprovider.value = "mtn";
                    				var selectedNetworkName = isprovider.value.trim();
                    				tickBulkSMSCarrier(selectedNetworkName);
                    				uniquePhoneNos.push(splitRawPhoneNos[i]);
                    			}
                    	
          			          	if(carrierAirtel.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                    				isprovider.value = "airtel";
                    				var selectedNetworkName = isprovider.value.trim();
                    				tickBulkSMSCarrier(selectedNetworkName);
                    				uniquePhoneNos.push(splitRawPhoneNos[i]);
                    			}
                    	
                    			if(carrierGlo.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                 				   	isprovider.value = "glo";
                    				var selectedNetworkName = isprovider.value.trim();
                    				tickBulkSMSCarrier(selectedNetworkName);
                    				uniquePhoneNos.push(splitRawPhoneNos[i]);
                    			}
                    	
                   			 	if(carrier9mobile.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1){
                    				isprovider.value = "9mobile";
                    				var selectedNetworkName = isprovider.value.trim();
                    				tickBulkSMSCarrier(selectedNetworkName);
                    				uniquePhoneNos.push(splitRawPhoneNos[i]);
                    			}
                    		}else{
                    			if((carrierMTN.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1) && ("mtn" == isprovider.value.trim())){
                    				uniquePhoneNos.push(splitRawPhoneNos[i]);
                    			}
                    	
                    			if((carrierAirtel.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1) && ("airtel" == isprovider.value.trim())){
                    				uniquePhoneNos.push(splitRawPhoneNos[i]);
                    			}
                    	
                    			if((carrierGlo.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1) && ("glo" == isprovider.value.trim())){
                    				uniquePhoneNos.push(splitRawPhoneNos[i]);
                    			}
                    	
                    			if((carrier9mobile.indexOf(splitRawPhoneNos[i].substring(1,4)) !== -1) && ("9mobile" == isprovider.value.trim())){
                   				 	uniquePhoneNos.push(splitRawPhoneNos[i]);
                    			}
                    		}
                    	}
                    }
                }
            }
        }
          
        phoneNoCountSpan.innerHTML = "Phone Number Count: " + uniquePhoneNos.length;

        filteredPhoneNos.value = uniquePhoneNos.join(",");
    }

    function restructureBulkSMSPhoneNumbers(){
        var rawPhoneNos = document.getElementById("phone-numbers");
        var filteredPhoneNos = document.getElementById("filtered-phone-numbers");
        if(filteredPhoneNos.value.trim().length > 1){
            rawPhoneNos.value = filteredPhoneNos.value;
            setTimeout(() => {
                alert("Phone number restructured successfully");
            }, 300);
        }else{
        	setTimeout(() => {
        		alert("Invalid or incomplete Phone number, restructure failed");
        	}, 300);
        }
    }

    function filterBulkSMSMessage(){
        var textMessage = document.getElementById("text-message");
        var textMessageSpan = document.getElementById("text-message-span");
        
        var textMsgCount = 160 * 3;
        setInterval(() => {
            if(textMessage.value.length > textMsgCount){
                textMessage.value = textMessage.value.substring(0, textMsgCount);
            }
        }, 100);

        textMessageSpan.innerHTML = "Word Count: " + textMessage.value.length + "/" + textMsgCount;

        filteredPhoneNos.value = uniquePhoneNos.join(",");
    }
	    
	
	function bypassBulkSMSPhoneNumbers(){
		filterBulkSMSPhoneNumbers();
		restructureBulkSMSPhoneNumbers();
		filterBulkSMSMessage();
	}
	
    function tickBulkSMSCarrier(networkName){
        var smsMessage = document.getElementById("text-message");
        var phoneNos = document.getElementById("filtered-phone-numbers");
        var isprovider = document.getElementById("isprovider");

        if(networkName.trim().length >= 1){
        isprovider.value = networkName;
        	carrierBulkSMSInfo(networkName,smsMessage.value,phoneNos.value);
        }else{
        	carrierBulkSMSInfo(isprovider.value,smsMessage.value,phoneNos.value);
        }
        filterBulkSMSPhoneNumbers();
        restructureBulkSMSPhoneNumbers();
        filterBulkSMSMessage();
    }
    
    function carrierBulkSMSInfo(ispName,smsMessage,phoneNos){
        var ispNames = ["airtel","mtn","glo","9mobile"];
        var proceedBtn = document.getElementById("proceedBtn");
        var isprovider = document.getElementById("isprovider");
        var textMessage = document.getElementById("text-message");
        var smsType = document.getElementById("sms-type");
        var senderId = document.getElementById("sender-id");
        
        for(x=0; x < ispNames.length; x++){
            var notTickedService = document.getElementById(ispNames[x]+"-lg");
            
            if(ispNames[x] !== ispName){
            	if(ispName.trim().length >= 1){
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.style = "filter: grayscale(100%);";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }else{
                notTickedService.src = "/asset/"+ispNames[x]+".png";
                notTickedService.classList.remove("br-radius-5px");
                notTickedService.classList.add("br-radius-100px");
                isprovider.value = "";
                }
            }else{
            	if(ispName.trim().length >= 1){
                notTickedService.style = "filter: grayscale(0%);";
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }else{
                notTickedService.classList.remove("br-radius-100px");
                notTickedService.classList.add("br-radius-5px");
                }
            }
        }

        var productStatus;
        if(ispName.length >= 1){
            var ispImage = document.getElementById(ispName+"-lg");
            ispImage.src = "/asset/"+ispName+"-marked.png";
            isprovider.value = ispName;
            if(ispImage.getAttribute("product-status") != "enabled"){
                productStatus = "disabled";
                ispImage.style = "filter: grayscale(100%);";
                document.getElementById("product-status-span").innerHTML = "Product unavalable at the moment!";
            }else{
                productStatus = "enabled";
                ispImage.style = "filter: grayscale(0%);";
                document.getElementById("product-status-span").innerHTML = "";
            }
        }else{
            productStatus = "disabled";
            document.getElementById("product-status-span").innerHTML = "";
        }
        splitFilteredPhoneNos = phoneNos.split(","); 
        
        if((senderId.value.length >= 1) && (textMessage.value.length >= 1) && (smsType.value.length >= 1) && (splitFilteredPhoneNos.length >= 1) && (smsMessage.length <= 160) && (isprovider.value.length >= 1) && (ispNames.indexOf(isprovider.value) !== -1) && (productStatus === "enabled")){
            proceedBtn.style = "pointer-events: auto;";
        }else{
            proceedBtn.style = "pointer-events: none;";
        }
    }
	
    function customJsRedirect(redirectLink, redirectDialog){
        var refinedDialog;
        if(redirectDialog.length > 0){
            refinedDialog = redirectDialog;
        }else{
            refinedDialog = "Are you sure you want to redirect this page?";
        }

        if(confirm(refinedDialog)){
            if(redirectLink.length > 0){
                window.location.href = redirectLink;
            }else{
                alert("Invalid Link");
            }
        }else{
            alert("Operation Cancelled");
        }
    }
    