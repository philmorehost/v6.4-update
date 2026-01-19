
function openForm(evt, formName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(formName).style.display = "block";
    evt.currentTarget.className += " active";
}

function openTransactionTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("transaction-tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.querySelectorAll(".transactions-section .tab");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

function selectCurrency(currency) {
    var currencyTabs = document.getElementsByClassName("currency-tab");
    for (var i = 0; i < currencyTabs.length; i++) {
        currencyTabs[i].classList.remove("active");
    }
    event.currentTarget.classList.add("active");

    // Fetch and update balance
    fetch("/api/get_wallet_balance.php?currency=" + currency)
        .then(response => response.json())
        .then(data => {
            if (data.balance !== undefined) {
                var formattedBalance = new Intl.NumberFormat('en-NG', { style: 'currency', currency: data.currency }).format(data.balance);
                document.querySelector(".balance-card .amount").textContent = formattedBalance;
                document.querySelector(".balance-card .ledger-balance").textContent = "Ledger balance: " + formattedBalance;
            }
        })
        .catch(error => {
            console.error("Error fetching balance:", error);
        });

    // Fetch and update transactions
    fetch("/api/get_transactions.php?currency=" + currency)
        .then(response => response.json())
        .then(data => {
            var tbody = document.querySelector("#transactions table tbody");
            tbody.innerHTML = ""; // Clear existing rows
            if (data.length > 0) {
                data.forEach(transaction => {
                    var row = `<tr>
                        <td>${transaction.reference}</td>
                        <td>${transaction.amount} ${transaction.destination_currency || transaction.crypto_currency}</td>
                        <td>${transaction.description}</td>
                        <td><span class="status-badge status-${transaction.status.toLowerCase()}">${transaction.status}</span></td>
                        <td>${new Date(transaction.date).toLocaleString()}</td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No transactions found.</td></tr>';
            }
        })
        .catch(error => {
            console.error("Error fetching transactions:", error);
        });
}

document.getElementById("copy-referral-link").addEventListener("click", function(event) {
    event.preventDefault();
    var referralLink = this.dataset.link;
    navigator.clipboard.writeText(referralLink).then(function() {
        alert("Referral link copied to clipboard!");
    }, function(err) {
        console.error("Could not copy text: ", err);
    });
});

document.getElementById("create-payment-link-form").addEventListener("submit", function(event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch("/api/create_payment_link.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.payment_link) {
            document.getElementById("payment-link-url").value = data.payment_link;
            document.getElementById("payment-link-result").style.display = "block";
        } else {
            alert("Error creating payment link: " + data.error);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while creating the payment link.");
    });
});

function openDepositForm(evt, formName) {
    var i, tabcontent, tablinks;
    tabcontent = document.querySelectorAll("#depositModal .tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.querySelectorAll("#depositModal .tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(formName).style.display = "block";
    evt.currentTarget.className += " active";
}

document.getElementById("convert-currency-form").addEventListener("submit", function(event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch("/api/convert_currency.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Currency converted successfully!");
            // Refresh the balance
            selectCurrency(document.querySelector(".currency-tab.active").dataset.currency);
        } else {
            alert("Error converting currency: " + data.error);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while converting the currency.");
    });
});

document.getElementById("generate-vacct-btn").addEventListener("click", function() {
    var currency = document.querySelector(".currency-tab.active").dataset.currency;
    fetch("/api/get_virtual_account.php?currency=" + currency)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert("Error generating virtual account: " + data.error);
            } else {
                var detailsDiv = document.getElementById("virtual-account-details");
                detailsDiv.innerHTML = `
                    <p><strong>Bank Name:</strong> ${data.bank_name}</p>
                    <p><strong>Account Number:</strong> ${data.account_number}</p>
                    <p><strong>Account Name:</strong> ${data.account_name}</p>
                `;
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while generating the virtual account.");
        });
});
