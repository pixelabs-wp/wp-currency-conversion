// Get the input fields
const crc_giveValInput = document.getElementById('give_val');
const crc_getValInput = document.getElementById('get_val');
const crc_conversionInput = document.getElementById('crc_conversion');
const crc_feeInput = document.getElementById('crc_fee');
const crc_minimumAmountInput = document.getElementById('minimum_amount');
var crc_give_currency_element =  document.getElementById('get_currency');
if(crc_give_currency_element)
{
    crc_give_currency = crc_give_currency_element.innerHTML;
}
const crc_amount_sent = document.getElementById('crc_amount_sent');
const crc_converion_rate = document.getElementById('crc_converion_rate');
const crc_total_amount = document.getElementById('crc_total_amount');
const crc_platform_fee = document.getElementById('crc_platform_fee');
const crc_amount_recieve = document.getElementById('crc_amount_recieve');
var  crc_link_btn = document.getElementById('crc_link_btn');

if (crc_giveValInput) {
    crc_giveValInput.addEventListener('input', crc_calculateGetVal);
}
document.addEventListener("DOMContentLoaded", function () {

    // Get the input element
    var inputElement = document.getElementById('give_val');

    if (inputElement) {
        inputElement.addEventListener('change', handleInputValidation);
    }



    // Event listener to close sidebar when clicking outside of it
    document.addEventListener("click", function (event) {
        const sidebar_left = document.getElementById("sidebar-left");
        const toggleButton = document.getElementById("toggle_left_button");
        if (sidebar_left) {
            const isClickInsideSidebar = sidebar_left.contains(event.target);
            const isClickOnToggleButton = toggleButton.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggleButton) {
                var sidebar_left_content = document.getElementById('q-item-left');
                var cross_icons = document.getElementById("crc-close-btn-left");
                cross_icons.style.display = 'none';
                sidebar_left.style.width = '0';
                sidebar_left_content.style.display = 'none';
            }
        }

        const sidebar_right = document.getElementById("sidebar-right");
        const toggleButton_right = document.getElementById("toggle_right_button");
        if (sidebar_right) {
            const isClickInsideSidebar_right = sidebar_right.contains(event.target);
            const isClickOnToggleButton_right = toggleButton_right.contains(event.target);


            if (!isClickInsideSidebar_right && !isClickOnToggleButton_right) {
                var sidebar_right_content = document.getElementById('q-item-right');
                var cross_icons_right = document.getElementById("crc-close-btn-right");
                cross_icons_right.style.display = 'none';
                sidebar_right.style.width = '0';
                sidebar_right_content.style.display = 'none';
            }
        }

    });
});


function handleInputValidation(event) {
    var inputElement = event.target;
    var defaultMinValue = parseFloat(inputElement.getAttribute('min'));
    var inputValue = parseFloat(inputElement.value);

    // Check if input value is less than the default minimum value
    if (inputValue < defaultMinValue) {
        // If less than the default minimum value, set input value to the default minimum value
        inputElement.value = defaultMinValue;
    }
}

// Function to trigger the input event listener
function triggerInputValidation() {
    // Get the input element
    var inputElement = document.getElementById('give_val');
    // Create a new input event
    var inputEvent = new Event('input', {
        bubbles: true,
        cancelable: true,
    });
    // Dispatch the input event on the input element
    inputElement.dispatchEvent(inputEvent);
}

function crc_calculateGetVal() {

    crc_currencyIdLeft = document.getElementById("left-currency-id");
    crc_currencyIdRight = document.getElementById("right-currency-id");


    if (crc_currencyIdLeft.value != "" && crc_currencyIdRight.value != "") {
        // Get the conversion and fee values
        const crc_minimum_amount = parseFloat(crc_minimumAmountInput.value);
        const crc_conversion = parseFloat(crc_conversionInput.value);
        const crc_fee = parseFloat(crc_feeInput.value);
        const crc_giveValue = parseFloat(crc_giveValInput.value);

        if (crc_giveValue >= crc_minimum_amount) {
            // Get the value entered in the giveValInput

            // Perform the calculation
            const crc_getValue = (crc_giveValue * crc_conversion) - crc_fee;

            // Update the getValInput value with the calculated result
            crc_getValInput.value = crc_getValue; // Round to 2 decimal places
            next_button = document.getElementById("next_button");

            next_button.classList.remove('crc_disabled');

        }
        else {
            next_button.classList.add('crc_disabled');
        }
    } else {
        next_button.classList.add('crc_disabled');
    }


}


function toggleSidebar_left() {
    var sidebar_left = document.getElementById('sidebar-left');
    var sidebar_left_content = document.getElementById('q-item-left');
    var cross_icons = document.getElementById("crc-close-btn-left");
    cross_icons.style.display = cross_icons.style.display === 'grid' ? 'none' : 'grid';
    sidebar_left.style.width = sidebar_left.style.width === '300px' ? '0' : '300px';
    sidebar_left_content.style.display = sidebar_left_content.style.display === 'flex' ? 'none' : 'flex';
}

function toggleSidebar_right() {
    var sidebar_right = document.getElementById('sidebar-right');
    var sidebar_right_content = document.getElementById('q-item-right');
    var cross_icons = document.getElementById("crc-close-btn-right");
    cross_icons.style.display = cross_icons.style.display === 'grid' ? 'none' : 'grid';
    sidebar_right.style.width = sidebar_right.style.width === '300px' ? '0' : '300px';
    sidebar_right_content.style.display = sidebar_right_content.style.display === 'flex' ? 'none' : 'flex';
}

function openPopup() {
    var crc_conversion = parseFloat(crc_conversionInput.value);
    var crc_fee = parseFloat(crc_feeInput.value);
    // Get the value entered in the giveValInput
    var crc_giveValue = parseFloat(crc_giveValInput.value);
    // Perform the calculation
    var crc_totalValue = crc_giveValue * crc_conversion;
    var crc_getValue = (crc_giveValue * crc_conversion) - crc_fee;
    crc_currencyIdLeft = document.getElementById("left-currency-id");
    crc_currencyIdRight = document.getElementById("right-currency-id");

    if (crc_currencyIdLeft.value == "" && crc_currencyIdRight.value == "") {
        crc_conversion = 0;
        crc_fee = 0;
        crc_getValue = 0;
        crc_totalValue = 0;
        crc_giveValue = 0;
        crc_link_btn.href  = "#";
    }
    document.getElementById('popup').style.display = 'block';
    document.getElementById('popup-overlay').style.display = 'block';
    crc_amount_sent.innerHTML = crc_giveValue;
    crc_converion_rate.innerHTML = crc_conversion;
    crc_total_amount.innerHTML = crc_totalValue;
    crc_platform_fee.innerHTML = crc_fee;
    crc_amount_recieve.innerHTML = crc_getValue
    crc_link_btn.href  = document.getElementById("currency_redirect_link").value;
}

function closePopup() {
    document.getElementById('popup').style.display = 'none';
    document.getElementById('popup-overlay').style.display = 'none';
}

// JavaScript function to handle currency option click
function handleCurrencyOptionClick( currency_symbol, inputId, id, currencyId, currencyName, currencyLogo, side) {

    

    // Update the target div with the selected currency information
    if (side == "left") {
        document.getElementById(id).innerHTML = `
        <img data-v-4e76d53a="" width="24" height="24" src="${currencyLogo}" id="give_logo" alt="" style="border-radius: 50%;  margin-right: 10px;">
        <span data-v-4e76d53a="" class="name" id="give_currency">${currencyName}</span>`;
        document.getElementById("left-currency-id").value = currencyId;
        document.getElementById("currency_symbol_left").innerHTML = currency_symbol;
    }
    else if (side == "right") {
        document.getElementById(id).innerHTML = `
        <img data-v-4e76d53a="" width="24" height="24" src="${currencyLogo}" id="get_logo" alt="" style="border-radius: 50%;  margin-right: 10px;">
        <span data-v-4e76d53a="" class="name" id="get_currency">${currencyName}</span>`;
        document.getElementById("right-currency-id").value = currencyId;
        document.getElementById("currency_symbol_right").innerHTML = currency_symbol;
        crc_get_rates();
    }
    crc_button = document.getElementById("swap_currencies");
    right = document.getElementById("right-currency-id").value;
    left = document.getElementById("left-currency-id").value
    if (right != "" && left != "") {
        crc_button.classList.remove('crc_disabled');
    }
    else {
        crc_button.classList.add('crc_disabled');
    }

    document.getElementById(inputId).value = currencyId;
    const currencyOptions = document.querySelectorAll('.currency-option');

    if (side == "left") {
        document.getElementById("vtov").innerHTML = '<span data-v-4e76d53a="" id="give_currency" class="name">'+crc_give_currency+'</span>';
        document.getElementById("right-currency-id").value = "";
        check_availibility();
        toggleSidebar_left();
    }
    else {
        toggleSidebar_right();

    }

}
function check_availibility() {
    currencyIdLeft = document.getElementById("left-currency-id").value;
    console.log(currencyIdLeft);
    jQuery.ajax({
        url: crc_ajax_object.ajax_url,
        type: 'post',
        data: {
            action: 'crc_get_currency_conversions',
            left_currency_id: currencyIdLeft
        },
        success: function (response) {
            if (response.success) {
                var availableSecondCurrencyIds = response.data;
                // Enable/disable currencies based on available conversions
                var currencyOptions = document.querySelectorAll('.currency-option[data-side="right"]');
                currencyOptions.forEach(option => {
                    var currencyId = parseInt(option.dataset.currencyId);

                    if (availableSecondCurrencyIds.includes(currencyId.toString())) {
                        option.classList.remove('disabled');
                    } else {
                        option.classList.add('disabled');
                    }
                });
            } else {
                console.log('Error:', response.data);
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('AJAX Error:', errorThrown);
        }
    });
}

function crc_get_rates() {
    currencyIdLeft = document.getElementById("left-currency-id").value;
    currencyIdRight = document.getElementById("right-currency-id").value;
    jQuery.ajax({
        url: crc_ajax_object.ajax_url,
        type: 'post',
        data: {
            action: 'crc_switch_currency_conversions',
            left_currency_id: currencyIdLeft,
            right_currency_id: currencyIdRight,
            type: "select"
        },
        success: function (response) {
            if (response.success) {
                document.getElementById("crc_rates").innerHTML = response.data.conversion_rate;
                document.getElementById("crc_fees").innerHTML = response.data.platform_fee;
                document.getElementById("vmin").innerHTML = response.data.minimum_amount;
                document.getElementById("crc_conversion").value = response.data.conversion_rate;
                document.getElementById("minimum_amount").value = response.data.minimum_amount;
                document.getElementById("crc_fee").value = response.data.platform_fee;
                document.getElementById('give_val').min = response.data.minimum_amount;
                document.getElementById("currency_redirect_link").value = response.data.currency_redirect_link;
                
                crc_link_btn.href  = response.data.currency_redirect_link;
                crc_calculateGetVal();
            } else {
                document.getElementById("crc_rates").innerHTML = "0";
                document.getElementById("crc_fees").innerHTML = "0";
                alert("No Rates available at the moment");

            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('AJAX Error:', errorThrown);
        }
    });
}
// Add click event listener to swap button
var crc_swap_currencies = document.getElementById("swap_currencies");
if (crc_swap_currencies) {
    crc_swap_currencies.addEventListener("click", swapCurrencies);
}

// Function to swap currencies
function swapCurrencies() {
    const give_logo = document.getElementById('give_logo').getAttribute('src');
    const give_currency = document.getElementById("give_currency").innerHTML;

    const get_logo = document.getElementById('get_logo').getAttribute('src');
    const get_currency = document.getElementById("get_currency").innerHTML;

    const left_currency_id = document.getElementById("left-currency-id").value;
    const right_currency_id = document.getElementById("right-currency-id").value;

    const left_currency_symbol = document.getElementById("currency_symbol_left").innerHTML;
    const right_currency_symbol = document.getElementById("currency_symbol_right").innerHTML;
    jQuery.ajax({
        url: crc_ajax_object.ajax_url,
        type: 'post',
        data: {
            action: 'crc_switch_currency_conversions',
            left_currency_id: left_currency_id,
            right_currency_id: right_currency_id,
            type: 'switch'
        },
        success: function (response) {
            if (response.success) {
                document.getElementById('get_logo').setAttribute('src', give_logo);
                document.getElementById('give_logo').setAttribute('src', get_logo);
                document.getElementById("get_currency").innerHTML = give_currency;
                document.getElementById("give_currency").innerHTML = get_currency;
                document.getElementById("left-currency-id").value = right_currency_id;
                document.getElementById("right-currency-id").value = left_currency_id;
                document.getElementById("crc_rates").innerHTML = response.data.conversion_rate;
                document.getElementById("crc_fees").innerHTML = response.data.platform_fee;
                document.getElementById("vmin").innerHTML = response.data.minimum_amount;
                document.getElementById("crc_conversion").value = response.data.conversion_rate;
                document.getElementById("minimum_amount").value = response.data.minimum_amount;
                document.getElementById('give_val').min = response.data.minimum_amount;
                document.getElementById("crc_fee").value = response.data.platform_fee;
                document.getElementById("currency_redirect_link").value = response.data.currency_redirect_link;
                document.getElementById("currency_symbol_left").innerHTML = right_currency_symbol;
                document.getElementById("currency_symbol_right").innerHTML = left_currency_symbol;
                crc_link_btn.href  = response.data.currency_redirect_link;
                //to change
                triggerInputValidation();
                crc_calculateGetVal();
                check_availibility();

            } else {
                alert("Conversion FROM " + get_currency + " To " + give_currency + " is not available at the moment")
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('AJAX Error:', errorThrown);
        }
    });
}

    function copyValue() {
        // Get the value of crc_amount_recieve
        var amountValue = document.getElementById("crc_amount_recieve").innerText;
        // Create a temporary textarea element
        var tempInput = document.createElement("textarea");
        // Set the value of the textarea to the amount value
        tempInput.value = amountValue;
        // Append the textarea to the document body
        document.body.appendChild(tempInput);
        // Select the text in the textarea
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); /* For mobile devices */
        // Copy the selected text to the clipboard
        document.execCommand("copy");
        // Remove the temporary textarea
        document.body.removeChild(tempInput);
    }
