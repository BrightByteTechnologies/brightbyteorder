var basket = document.getElementsByClassName("basket")[0];
var basketBtn = document.getElementById("basketBtn");
var basketClose = document.getElementById("basket-close");
var itemsInBasket = document.getElementById("items-in-basket");
var payButton = document.getElementById("pay-button");
var resetButton = document.getElementById("reset-button");
var overlay = document.getElementById("basket-overlay");
var basketCount = document.getElementById("basketCount");
var removeBtn = document.getElementsByClassName("minusBtn");

var products;
var basketItems = {};

jQuery.ajax({
    type: "GET",
    url: 'getProducts.php',
    data: { functionName: 'getProducts' },
    success: function (response) {
        products = JSON.parse(response);
    }
});

function order() {
    var itemName = event.currentTarget.querySelector(".item-name").textContent;
    var itemDesc = event.currentTarget.querySelector(".item-description").textContent;

    itemName = DOMPurify.sanitize(itemName); // Sanitize the strings
    itemDesc = DOMPurify.sanitize(itemDesc);

    overlay.style.display = "block";

    // Create amount selection element
    const amountSele = document.createElement("div");
    amountSele.classList.add("amountSele");
    amountSele.setAttribute("id", "toClose");
    amountSele.textContent = "Menge:";

    // Create the input element (Number)
    amountInput = document.createElement("input");
    amountInput.setAttribute("type", "number");
    amountInput.setAttribute("id", "itemAmount");
    amountInput.setAttribute("value", 1);
    amountInput.setAttribute("oninput", "validity.valid||(value='')")
    amountInput.setAttribute("min", 1);
    amountInput.setAttribute("max", 20);
    amountSele.appendChild(amountInput);

    // Create the submit button
    amountSub = document.createElement("input");
    amountSub.setAttribute("value", "In den Warenkorb");
    amountSub.setAttribute("type", "submit");

    amountSub.addEventListener("click", function () {
        // Get selected amount and update basket
        var itemAmount = parseInt(document.getElementById("itemAmount").value);
        if (itemAmount > 0 && itemAmount != NaN) {
            if (parseInt(basketCount.textContent) + itemAmount > 20) {
                closeAmountSelection(amountSele);
                alert("Maximal 20 Getränke pro Person!");
                return;
            }
        } else {
            alert("Eingabe kann nicht leer sein");
            return;
        }

        updateBasket(itemName, itemDesc, itemAmount);

        // Close amount selection element
        closeElement();
    });

    amountSele.appendChild(amountSub);
    document.body.appendChild(amountSele);
}

function openBasket() {
    basket.style.display = "block";
    overlay.style.display = "block";
    basket.setAttribute("id", "toClose");
}

function updateBasket(itemName, itemDesc, itemAmount) {
    if (isInProducts(itemName, itemDesc)) {
        var itemPrice = findEntry(itemName, itemDesc)["totalPrice"];
        var parsedPrice = parseFloat(itemPrice.replace(',', '.'));

        var basketName = `${DOMPurify.sanitize(itemName)} | ${DOMPurify.sanitize(itemDesc)}`;
        var itemFound = false;

        for (var key in basketItems) {
            if (key === basketName) {
                basketItems[key].quantity += itemAmount;
                basketItems[key].totalPrice += parsedPrice * itemAmount;
                itemFound = true;
                break;
            }
        }

        if (!itemFound) {
            basketItems[basketName] = {
                name: itemName,
                description: itemDesc,
                price: parsedPrice,
                quantity: itemAmount,
                totalPrice: parsedPrice * itemAmount
            };
        }

        createNotification("Item zum Warenkorb hinzugefügt.");
        flashBasket("white");
        updateBasketCount();

        // Create HTML elements for displaying the items in the basket
        var itemsInBasket = document.getElementById("items-in-basket");
        itemsInBasket.innerHTML = "";
        for (var key in basketItems) {
            var item = basketItems[key];
            var itemRow = document.createElement("tr");

            var itemNameCell = document.createElement("td");
            itemNameCell.textContent = item.name + " | " + item.description;
            itemRow.appendChild(itemNameCell);

            var itemQuantityCell = document.createElement("td");
            itemQuantityCell.textContent = item.quantity;
            itemRow.appendChild(itemQuantityCell);

            var itemPriceCell = document.createElement("td");
            itemPriceCell.textContent = item.price;
            itemRow.appendChild(itemPriceCell);

            var itemTotalPriceCell = document.createElement("td");
            itemTotalPriceCell.textContent = item.totalPrice;
            itemRow.appendChild(itemTotalPriceCell);

            // Create the button to remove item
            var minusCell = document.createElement("button");
            minusCell.textContent = "✖";
            minusCell.setAttribute("id", "minusBtn");
            minusCell.addEventListener("click", function () {
                itemRow.remove();
                updateBasketCount();
            });
            itemRow.appendChild(minusCell);

            itemsInBasket.appendChild(itemRow);
        }

    } else {
        createNotification("Kann nicht hinzugefügt werden!");
        flashBasket("red");
    }
}

function updateBasketCount() {
    var count = 0;
    for (var key in basketItems) {
        count += basketItems[key].quantity;
    }
    basketCount.textContent = count;
}

function isInProducts(name, desc) {
    return products.some(item => item.name === name && item.description === desc);
}
function findEntry(name, desc) {
    return products.find(p => p.name === name && p.description === desc);
}

function flashBasket(color) {
    // Visual effect
    basketBtn.classList.add(`${color}-flash`);
    setTimeout(function () {
        basketBtn.classList.remove(`${color}-flash`);
    }, 500);
}

function createNotification(textContent) {
    var content = DOMPurify.sanitize(textContent);

    const notification = document.createElement('div');
    notification.textContent = content;
    notification.classList.add('notification');
    notification.classList.add('swipeup');
    document.body.appendChild(notification);

    // Remove notification after a delay
    setTimeout(() => {
        document.body.removeChild(notification);
    }, 1000);
}

function closeElement() {
    const closeElement = document.getElementById("toClose");
    closeElement.style.display = "none";
    overlay.style.display = "none";
    closeElement.removeAttribute("id");
    if (closeElement.className === "amountSele") {
        closeElement.parentNode.removeChild(closeElement);
    }
}

function resetConfirmation() {
    resetButton.setAttribute("disabled", "");
    resetDiv = document.createElement("div");
    resetDiv.classList.add("reset");
    basket.appendChild(resetDiv);

    var questionSpan = document.createElement("span");
    questionSpan.setAttribute("id", "resetSpan");
    questionSpan.textContent = "Warenkorb leeren?";
    resetDiv.appendChild(questionSpan);

    resetCheck = document.createElement("input");
    resetCheck.setAttribute("id", "resetCheck");
    resetCheck.setAttribute("value", "Ja");
    resetCheck.setAttribute("type", "submit");
    resetDiv.appendChild(resetCheck);

    resetCancel = document.createElement("input");
    resetCancel.setAttribute("id", "resetCancel");
    resetCancel.setAttribute("value", "Nein");
    resetCancel.setAttribute("type", "submit");
    resetDiv.appendChild(resetCancel);

    resetCheck.addEventListener("click", function () {
        var table = document.getElementById("items-in-basket");
        resetBasket();
        basket.removeChild(resetDiv);
        resetButton.removeAttribute("disabled", "");
    })
    resetCancel.addEventListener("click", function () {
        basket.removeChild(resetDiv);
        resetButton.removeAttribute("disabled", "");
    })
}

function resetBasket() {
    var items = document.querySelectorAll("#items-in-basket tr");
    for (var i = 0; i < items.length; i++) {
        items[i].remove();
    }
    updateBasketCount();
}

function remove() {
    var parentRow = event.target.parentNode;
    parentRow.remove();
}

overlay.addEventListener("click", function () {
    closeElement();
});

payButton.addEventListener("click", function () {
    // Open payment window
    alert("Payment window opened.");
    // Reset basket
    resetBasket();
});

resetButton.addEventListener("click", function () {
    // Reset basket
    resetConfirmation();
});

basketBtn.addEventListener("click", function () {
    // Open basket
    openBasket();
});

basketClose.addEventListener("click", function () {
    // Close basket
    closeElement();
});
