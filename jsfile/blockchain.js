function fetchWWalletAddress() {
  const crypto_deposit_amount = document.getElementById("crypto-deposit-amount");
  const crypto_deposit_currency = document.getElementById("crypto-deposit-currency");
  const crypto_deposit_address_div = document.getElementById("crypto-deposit-address-div");
  const crypto_deposit_instruction_span = document.getElementById("crypto-deposit-instruction-span");
  const crypto_deposit_address_span = document.getElementById("crypto-deposit-address-span");
  const crypto_deposit_chain_span = document.getElementById("crypto-deposit-chain-span");

  crypto_deposit_address_div.style.display = "none";
  crypto_deposit_instruction_span.innerText = "";
  crypto_deposit_address_span.innerText = "";
  crypto_deposit_chain_span.innerText = "";


  if (crypto_deposit_amount.value != "") {
    if (Number(crypto_deposit_amount.value)) {
      if (Number(crypto_deposit_amount.value) > 0) {
        if (crypto_deposit_currency.value != "") {
          fetch('../web/api/crypto-wallets.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              currency: crypto_deposit_currency.value
            })
          }).then(res => res.json())
            .then(jsonRes => {
              if (jsonRes.status == "success") {
                crypto_deposit_instruction_span.innerText = "Please send " + jsonRes.data["payment_methods"]["currency"] + " to the following address:";
                crypto_deposit_address_span.innerText = jsonRes.data["payment_methods"]["address"];
                crypto_deposit_chain_span.innerText = jsonRes.data["payment_methods"]["chain"];
                crypto_deposit_address_div.style.display = "block";
              } else {
                Swal.fire('Warning!', jsonRes.desc, 'warning');
              }
            });
        } else {
          Swal.fire('Warning!', 'Deposit Currency is required', 'warning');
        }
      } else {
        Swal.fire('Warning!', 'Deposit Amount be greater than zero (0)', 'warning');
      }
    } else {
      Swal.fire('Warning!', 'Deposit Amount must be number', 'warning');
    }
  } else {
    Swal.fire('Warning!', 'Deposit Amount is required', 'warning');
  }
}

function getCurrencyChain(currency, select_id_name) {
  const select_tag = document.getElementById(select_id_name);
  const crypto_beneficiary_currency_img = document.getElementById("crypto-beneficiary-currency-img");
  crypto_beneficiary_currency_img.src = "../asset/" + currency.toLowerCase() + ".jpg";
  select_tag.innerHTML = "";
  fetch('../web/api/crypto-transfer-chain.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      currency: currency
    })
  }).then(res => res.json())
    .then(jsonRes => {
      if (jsonRes.status == "success") {
        const currency_chain_arr = Object.entries(jsonRes.data.chain);
        for (const [chain, interest] of currency_chain_arr) {
          let currency_chain_option = document.createElement("option");
          currency_chain_option.value = chain;
          currency_chain_option.innerHTML = chain.toUpperCase();
          select_tag.appendChild(currency_chain_option);
        }
      } else {
        Swal.fire('Warning', jsonRes.desc, 'warning');
      }
    });
}

function saveBeneficiary() {
  const crypto_beneficiary_label = document.getElementById("crypto-beneficiary-label");
  const crypto_beneficiary_currency = document.getElementById("crypto-beneficiary-currency");
  const crypto_beneficiary_chain = document.getElementById("crypto-beneficiary-chain");
  const crypto_beneficiary_address = document.getElementById("crypto-beneficiary-address");

  if (crypto_beneficiary_label.value != "") {
    if (crypto_beneficiary_currency.value != "") {
      if (crypto_beneficiary_chain.value != "") {
        if (crypto_beneficiary_address.value != "") {
          fetch('../web/api/crypto-beneficiary.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              label: crypto_beneficiary_label.value,
              currency: crypto_beneficiary_currency.value,
              chain: crypto_beneficiary_chain.value,
              address: crypto_beneficiary_address.value
            })
          }).then(res => res.json())
            .then(jsonRes => {
              if (jsonRes.status == "success") {
                Swal.fire('Success', jsonRes.desc, 'success');
                getBeneficiaries("crypto-transfer-beneficiary");
              } else {
                Swal.fire('Warning!', jsonRes.desc, 'warning');
              }
            });
        } else {
          Swal.fire('Warning!', 'Beneficiary Destination address is required', 'warning');
        }
      } else {
        Swal.fire('Warning!', 'Beneficiary Currency Chain is required', 'warning');
      }
    } else {
      Swal.fire('Warning!', 'Beneficiary currency required', 'warning');
    }
  } else {
    Swal.fire('Warning!', 'Beneficiary label is required', 'warning');
  }
}

function getBeneficiaries(select_id_name) {
  const select_tag = document.getElementById(select_id_name);
  select_tag.innerHTML = "";
  let beneficiary_default_option = document.createElement("option");
  beneficiary_default_option.value = "";
  beneficiary_default_option.innerHTML = "Choose Beneficiary";
  beneficiary_default_option.default = true;
  beneficiary_default_option.hidden = true;
  select_tag.appendChild(beneficiary_default_option);
  fetch('../web/api/crypto-beneficiaries.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    //body: JSON.stringify({
    //currency: currency
    //})
  }).then(res => res.json())
    .then(jsonRes => {
      if (jsonRes.status == "success") {
        const beneficiaries_data_arr = jsonRes.data;
        for (let i = 0; i < beneficiaries_data_arr.length; i++) {
          const beneficiary_arr = jsonRes.data[i];
          let beneficiary_option = document.createElement("option");
          beneficiary_option.value = beneficiary_arr["beneficiary_id"];
          beneficiary_option.innerHTML = beneficiary_arr["label"].toUpperCase();
          beneficiary_option.setAttribute("crypto-address", beneficiary_arr["address"]);
          beneficiary_option.setAttribute("crypto-chain", beneficiary_arr["chain"]);
          beneficiary_option.setAttribute("currency", beneficiary_arr["currency"]);
          select_tag.appendChild(beneficiary_option);
        }
      } else {
        Swal.fire('Warning', jsonRes.desc, 'warning');
      }
    });
}

getBeneficiaries("crypto-transfer-beneficiary");

function setCryptoBeneficiaryInfo(beneficiary_select_id) {
  const beneficiary_select_tag = document.getElementById(beneficiary_select_id);
  const crypto_beneficiary_currency_img = document.getElementById("crypto-transfer-beneficiary-currency-img");
  const crypto_beneficiary_currency = document.getElementById("crypto-transfer-beneficiary-currency");
  const crypto_beneficiary_chain = document.getElementById("crypto-transfer-beneficiary-chain");
  const crypto_beneficiary_address = document.getElementById("crypto-transfer-beneficiary-address");

  crypto_beneficiary_currency.value = "";
  crypto_beneficiary_chain.value = "";
  crypto_beneficiary_address.value = "";

  crypto_beneficiary_currency.value = beneficiary_select_tag.options[beneficiary_select_tag.selectedIndex].getAttribute("currency");
  crypto_beneficiary_currency_img.src = "../asset/" + crypto_beneficiary_currency.value.toLowerCase() + ".jpg";
  crypto_beneficiary_chain.value = beneficiary_select_tag.options[beneficiary_select_tag.selectedIndex].getAttribute("crypto-chain").toUpperCase();
  crypto_beneficiary_address.value = beneficiary_select_tag.options[beneficiary_select_tag.selectedIndex].getAttribute("crypto-address");
}

function getTransferFee() {
  const crypto_beneficiary_currency = document.getElementById("crypto-transfer-beneficiary-currency");
  const crypto_beneficiary_chain = document.getElementById("crypto-transfer-beneficiary-chain");
  const crypto_transfer_beneficiary_amount = document.getElementById("crypto-transfer-beneficiary-amount");

  const stamp_duty_span_details = document.getElementById("stamp-duty-span-details");
  stamp_duty_span_details.innerHTML = "";

  if (crypto_beneficiary_currency.value != "") {
    if (crypto_beneficiary_chain.value != "") {
      if (crypto_transfer_beneficiary_amount.value != "") {
        if (Number(crypto_transfer_beneficiary_amount.value)) {
          if (Number(crypto_transfer_beneficiary_amount.value) > 0) {

            fetch('../web/api/crypto-transfer-fee.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({
                "currency": crypto_beneficiary_currency.value,
                "chain": crypto_beneficiary_chain.value
              })
            }).then(res => res.json())
              .then(jsonRes => {
                if (jsonRes.status == "success") {
                  stamp_duty_span_details.innerHTML = "<br/><div class='container bg-light rounded-3 mt-2 py-2'><span class='text-dark fs-6'>You will be debited <strong>" + " for crypto transfer over " + crypto_beneficiary_currency.value.toUpperCase() + " currency and " + crypto_beneficiary_chain.value.toUpperCase() + "</strong><br/>Transfer fee: " + (jsonRes.data.fee / 100) + "<br/>Amount you'll be charged = <strong>" + (Number(crypto_transfer_beneficiary_amount.value) + Number((crypto_transfer_beneficiary_amount.value * (jsonRes.data.fee / 100)))) + " " + crypto_beneficiary_currency.value.toUpperCase() + "</span></div><br/>";

                  // Swal.fire('Warning!', jsonRes.desc, 'warning');
                } else {
                  stamp_duty_span_details.innerHTML = "<br/><span class='text-danger fw-bold'>" + jsonRes.desc + "</span><br/>";
                  // Swal.fire('Warning!', jsonRes.desc, 'warning');
                }
              });
          } else {
            stamp_duty_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Transfer Amount be greater than zero (0)</span><br/>";
            // Swal.fire('Warning!', '', 'warning');
          }
        } else {
          stamp_duty_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Transfer Amount must be number</span><br/>";
          // Swal.fire('Warning!', '', 'warning');
        }
      } else {
        stamp_duty_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Transfer Amount is required</span><br/>";
        // Swal.fire('Warning!', '', 'warning');
      }
    } else {
      stamp_duty_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Kindly fill all the form</span><br/>";
      // Swal.fire('Warning!', '', 'warning');
    }
  } else {
    stamp_duty_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Kindly fill all the form</span><br/>";
    // Swal.fire('Warning!', '', 'warning');
  }

}

function initiateCryptoTransfer() {
  const crypto_beneficiary_id = document.getElementById("crypto-transfer-beneficiary");
  const crypto_beneficiary_amount = document.getElementById("crypto-transfer-beneficiary-amount");
  const initiate_crypto_transfer = document.getElementById("initiate-crypto-transfer");

  if (crypto_beneficiary_id.value != "") {
    if (crypto_beneficiary_amount.value != "") {
      if (Number(crypto_beneficiary_amount.value)) {
        if (Number(crypto_beneficiary_amount.value) > 0) {


          Swal.fire({
            title: "Initiate Transfer?",
            text: "You're about to initiate a crypto transfer",
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "No, cancel!",
            confirmButtonText: "Proceed",
            closeOnConfirm: false
          }).then((result) => {
            if (result.isConfirmed) {
              initiate_crypto_transfer.style.display = "none";
              fetch('../web/api/crypto-transfer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  beneficiary_id: crypto_beneficiary_id.value,
                  amount: crypto_beneficiary_amount.value
                })
              }).then(res => res.json())
                .then(jsonRes => {
                  if (jsonRes.status == "success") {
                    getBeneficiaries("crypto-transfer-beneficiary");
                    setCryptoBeneficiaryInfo('crypto-transfer-beneficiary');
                    crypto_beneficiary_amount.value = "";
                    Swal.fire('Success', jsonRes.desc, 'success');
                    initiate_crypto_transfer.style.display = "inline-block";
                  } else {
                    initiate_crypto_transfer.style.display = "inline-block";
                    Swal.fire('Warning!', jsonRes.desc, 'warning');
                  }
                });
            } else if (result.dismiss == Swal.DismissReason.cancel) {
              JsLoadingOverlay.hide();
            }
          });
        } else {
          Swal.fire('Warning!', 'Transfer Amount be greater than zero (0)', 'warning');
        }
      } else {
        Swal.fire('Warning!', 'Transfer Amount must be number', 'warning');
      }
    } else {
      Swal.fire('Warning!', 'Transfer Amount is required', 'warning');
    }
  } else {
    Swal.fire('Warning!', 'Transfer Beneficiary is required', 'warning');
  }
}

function updateSourceCurrencyPreview(select) {
  const img = document.getElementById('source-currency-img');
  img.src = '../asset/' + select.value + '.jpg';
}

function updateTargetCurrencyPreview(select) {
  const img = document.getElementById('target-currency-img');
  img.src = '../asset/' + select.value + '.jpg';
}

function currencyConvertRates(functionType) {
  const convert_source_currency = document.getElementById("convert-source-currency");
  const convert_target_currency = document.getElementById("convert-target-currency");
  const amount_to_convert = document.getElementById("amount-to-convert");
  const expected_converted_amount = document.getElementById("expected-converted-amount");
  expected_converted_amount.value = "";
  const currency_convert_btn = document.getElementById("currency-convert-btn");
  const currency_swap_btn = document.getElementById("currency-swap-btn");
  currency_convert_btn.style.display = "inline-block";
  currency_swap_btn.style.display = "none";

  const swap_id = document.getElementById("swap-id");
  swap_id.value = "";
  const conversion_span_details = document.getElementById("conversion-span-details");
  conversion_span_details.innerHTML = "";

  if (functionType == "convert-currency") {
    JsLoadingOverlay.show();
    if (convert_source_currency.value != "") {
      if (convert_target_currency.value != "") {
        if (amount_to_convert.value != "") {
          if (Number(amount_to_convert.value)) {
            if (Number(amount_to_convert.value) > 0) {
              fetch('../web/api/crypto-conversion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  "source-currency": convert_source_currency.value,
                  "target-currency": convert_target_currency.value,
                  amount: amount_to_convert.value
                })
              }).then(res => res.json())
                .then(jsonRes => {
                  if (jsonRes.status == "success") {
                    JsLoadingOverlay.hide();

                    let expected_amount = 0;
                    if (jsonRes.type == "sell") {
                      if (amount_to_convert.value >= jsonRes.rate) {
                        expected_amount = (amount_to_convert.value / jsonRes.rate);
                        conversion_span_details.innerHTML = "<br/><div class='container bg-light rounded-3 mt-2 py-2'><span class='text-dark fs-6'>Amount we'll convert: <strong>" + jsonRes.rate + " " + convert_source_currency.value.toUpperCase() + " = " + expected_amount + " " + convert_target_currency.value.toUpperCase() + "</strong><br/>Conversion fee: " + (jsonRes.fee / 100) + "</strong><br/>Time to Convert: <strong>" + jsonRes["convert-time"] + " secs</strong></span></div><br/>";
                      } else {
                        JsLoadingOverlay.hide();
                        conversion_span_details.innerHTML = "<br/><span class='text-danger fw-bold'>Error: " + convert_source_currency.value.toUpperCase() + " must be atleast " + jsonRes.rate.toFixed(4) + "</span>";
                        // Swal.fire('Warning!', jsonRes.desc, 'warning');
                      }
                    } else {
                      expected_amount = (jsonRes.rate * amount_to_convert.value);
                      conversion_span_details.innerHTML = "<br/><div class='container bg-light rounded-3 mt-2 py-2'><span class='text-dark fs-6'>Amount we'll convert: <strong> 1 " + convert_source_currency.value.toUpperCase() + " = " + jsonRes.rate + " " + convert_target_currency.value.toUpperCase() + "</strong><br/>Conversion fee: " + (jsonRes.fee / 100) + "</strong><br/>Time to Convert: <strong>" + jsonRes["convert-time"] + " secs</strong></span></div><br/>";
                    }

                    expected_converted_amount.value = expected_amount;
                    currency_convert_btn.style.display = "none";
                    currency_swap_btn.style.display = "inline-block";
                    swap_id.value = jsonRes["swap-id"];
                    // Swal.fire('Success', jsonRes.desc, 'success');
                  } else {
                    JsLoadingOverlay.hide();
                    conversion_span_details.innerHTML = "<br/><span class='text-danger fw-bold'>" + jsonRes.desc + "</span>";
                    // Swal.fire('Warning!', jsonRes.desc, 'warning');
                  }
                });
            } else {
              JsLoadingOverlay.hide();
              conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Conversion Amount be greater than zero (0)</span>";
              // Swal.fire('Warning!', '', 'warning');
            }
          } else {
            JsLoadingOverlay.hide();
            conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Conversion Amount must be number</span>";
            // Swal.fire('Warning!', '', 'warning');
          }
        } else {
          JsLoadingOverlay.hide();
          conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Conversion Amount is required</span>";
          // Swal.fire('Warning!', '', 'warning');
        }
      } else {
        JsLoadingOverlay.hide();
        conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Kindly fill all the form</span>";
        // Swal.fire('Warning!', '', 'warning');
      }
    } else {
      JsLoadingOverlay.hide();
      conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Kindly fill all the form</span>";
      // Swal.fire('Warning!', '', 'warning');
    }
  }

}

function currencySwap() {
  JsLoadingOverlay.show();
  const convert_source_currency = document.getElementById("convert-source-currency");
  const convert_target_currency = document.getElementById("convert-target-currency");
  const amount_to_convert = document.getElementById("amount-to-convert");
  const currency_convert_btn = document.getElementById("currency-convert-btn");
  const currency_swap_btn = document.getElementById("currency-swap-btn");
  const swap_id = document.getElementById("swap-id");
  const conversion_span_details = document.getElementById("conversion-span-details");
  conversion_span_details.innerHTML = "";

  if (convert_source_currency.value != "") {
    if (convert_target_currency.value != "") {
      if (amount_to_convert.value != "") {
        if (Number(amount_to_convert.value)) {
          if (Number(amount_to_convert.value) > 0) {
            if (swap_id.value != "") {
              Swal.fire({
                title: "Initiate Swap?",
                text: "You're about to swap " + convert_source_currency.value.toUpperCase() + " to " + convert_target_currency.value.toUpperCase(),
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: "No, cancel!",
                confirmButtonText: "Proceed",
                closeOnConfirm: false
              }).then((result) => {
                if (result.isConfirmed) {
                  currency_swap_btn.style.display = "none";
                  fetch('../web/api/crypto-swap.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                      "swap-id": swap_id.value,
                      "amount": amount_to_convert.value
                    })
                  }).then(res => res.json())
                    .then(jsonRes => {
                      if (jsonRes.status == "success") {
                        currency_convert_btn.style.display = "inline-block";
                        currency_swap_btn.style.display = "none";
                        swap_id.value = "";
                        amount_to_convert.value = "";
                        Swal.fire('Success', jsonRes.desc, 'success');
                        setTimeout(() => {
                          window.location.reload();
                        }, 2000);
                      } else {
                        JsLoadingOverlay.hide();
                        currency_swap_btn.style.display = "inline-block";
                        conversion_span_details.innerHTML = "<br/><span class='text-danger fw-bold'>" + jsonRes.desc + "</span>";
                        // Swal.fire('Warning!', jsonRes.desc, 'warning');
                      }
                    });
                } else if (result.dismiss == Swal.DismissReason.cancel) {
                  JsLoadingOverlay.hide();
                }
              });
            } else {
              JsLoadingOverlay.hide();
              conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Error Swapping currency</span>";
              // Swal.fire('Warning!', '', 'warning');
            }
          } else {
            JsLoadingOverlay.hide();
            conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Conversion Amount be greater than zero (0)</span>";
            // Swal.fire('Warning!', '', 'warning');
          }
        } else {
          JsLoadingOverlay.hide();
          conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Conversion Amount must be number</span>";
          // Swal.fire('Warning!', '', 'warning');
        }
      } else {
        JsLoadingOverlay.hide();
        conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Conversion Amount is required</span>";
        // Swal.fire('Warning!', '', 'warning');
      }
    } else {
      JsLoadingOverlay.hide();
      conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Kindly fill all the form</span>";
      // Swal.fire('Warning!', '', 'warning');
    }
  } else {
    JsLoadingOverlay.hide();
    conversion_span_details.innerHTML = "<br/><span class='text-danger fs-6 fw-bold'>Oops: Kindly fill all the form</span>";
    // Swal.fire('Warning!', '', 'warning');
  }

}

function fetchDashboardWalletDetails(currency) {
  const available_balance = document.getElementById("available-balance");
  const ledger_balance = document.getElementById("ledger-balance");

  available_balance.innerText = "";
  ledger_balance.innerText = "";


  if (currency != "") {
    fetch('../web/api/crypto-wallets.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        currency: currency
      })
    }).then(res => res.json())
      .then(jsonRes => {
        if (jsonRes.status == "success") {
          available_balance.innerHTML = Number(jsonRes.data["wallet_balance"]).toFixed(2) + " " + jsonRes.data["currency"];
          ledger_balance.innerHTML = Number(jsonRes.data["ledger_balance"]).toFixed(2) + " " + jsonRes.data["currency"];
        } else {
          Swal.fire('Warning!', jsonRes.desc, 'warning');
        }
      });
  } else {
    Swal.fire('Warning!', 'Currency is required', 'warning');
  }
}

function selectCrypto(crypto) {

  // reset all currency btn
  for (let x = 0; x < document.querySelectorAll('.crypto-currency-btn').length; x++) {
    if (document.querySelectorAll('.crypto-currency-btn')[x].id === crypto + "-btn") {
      document.querySelectorAll('.crypto-currency-btn')[x].classList.remove('bg-white', 'text-dark');
      document.querySelectorAll('.crypto-currency-btn')[x].classList.add('bg-dark', 'text-white');
    } else {
      document.querySelectorAll('.crypto-currency-btn')[x].classList.remove('bg-dark', 'text-white');
      document.querySelectorAll('.crypto-currency-btn')[x].classList.add('bg-white', 'text-dark');
    }
  }

  // your existing function
  fetchDashboardWalletDetails(crypto);
}

selectCrypto("ngn");

function createPaymentLink() {
  const payment_amount = document.getElementById("payment-amount");
  const payment_currency = document.getElementById("payment-currency");
  const payment_description = document.getElementById("payment-description");
  const payment_link_result = document.getElementById("payment-link-result");

  if (payment_currency.value != "") {
    if (payment_description.value != "") {
      if (payment_amount.value != "") {
        if (Number(payment_amount.value)) {
          if (Number(payment_amount.value) > 0) {


            Swal.fire({
              title: "Create Payment Link?",
              text: "You're about to create a payment link for " + payment_currency.value.toUpperCase() + " " + payment_amount.value,
              icon: "warning",
              showCancelButton: true,
              cancelButtonText: "No, cancel!",
              confirmButtonText: "Proceed",
              closeOnConfirm: false
            }).then((result) => {
              if (result.isConfirmed) {
                fetch('../web/api/crypto-payment-link.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({
                    "currency": payment_currency.value,
                    "amount": payment_amount.value,
                    "desc": payment_description.value
                  })
                }).then(res => res.json())
                  .then(jsonRes => {
                    if (jsonRes.status == "success") {
                      Swal.fire('Success', jsonRes.desc, 'success');
                      payment_link_result.innerHTML = '<p>Here is your payment link: ' + jsonRes.url + '</p>';
                      payment_link_result.style.display = "block";
                      // setTimeout(() => {
                      //   window.open("PaymentLink.php");
                      // }, 2000);
                    } else {
                      JsLoadingOverlay.hide();
                      Swal.fire('Warning!', jsonRes.desc, 'warning');
                    }
                  });
              } else if (result.dismiss == Swal.DismissReason.cancel) {
                JsLoadingOverlay.hide();
              }
            });

          } else {
            JsLoadingOverlay.hide();
            Swal.fire('Warning!', 'Oops: Payment Amount be greater than zero (0)', 'warning');
          }
        } else {
          JsLoadingOverlay.hide();
          Swal.fire('Warning!', 'Oops: Payment Amount must be number', 'warning');
        }
      } else {
        JsLoadingOverlay.hide();
        Swal.fire('Warning!', 'Oops: Payment Amount is required', 'warning');
      }
    } else {
      JsLoadingOverlay.hide();
      Swal.fire('Warning!', 'Oops: Kindly fill all the form', 'warning');
    }
  } else {
    JsLoadingOverlay.hide();
    Swal.fire('Warning!', 'Oops: Kindly fill all the form', 'warning');
  }

}

function toggleActionPanel(panelId) {
  const getActionPanelDivClass = document.getElementsByClassName("action-panel");
  const getPanelDiv = document.getElementById(panelId);
  if (getPanelDiv.classList.contains("action-panel")) {
    for (let x = 0; x < getActionPanelDivClass.length; x++) {
      if (getActionPanelDivClass[x].id == panelId) {
        getActionPanelDivClass[x].style.display = "block";
      } else {
        getActionPanelDivClass[x].style.display = "none";
      }
    }
  }
}

function switchTransferTab(evt, tabName) {
  // Hide all tab contents
  document.querySelectorAll('.transfer-tab-content').forEach(tab => {
    tab.style.display = 'none';
  });

  // Remove active class from all tabs
  document.querySelectorAll('.tab').forEach(tab => {
    tab.classList.remove('active');
  });

  // Show selected tab
  document.getElementById(tabName).style.display = 'block';

  // Activate clicked tab
  evt.currentTarget.classList.add('active');
}
