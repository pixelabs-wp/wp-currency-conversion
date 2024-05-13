// Choose File Add Currency Start
jQuery("#currency_logo").bind("change", function () {
    var filename = jQuery("#currency_logo").val();
    if (/^\s*$/.test(filename)) {
        jQuery(".file-upload").removeClass("active");
        jQuery("#noFile").text("No file chosen...");
    } else {
        jQuery(".file-upload").addClass("active");
        jQuery("#noFile").text(filename.replace("C:\\fakepath\\", ""));
    }
});
// Choose File Add Currency End

// Choose Option Add Rates to Currency Form Currency Start
jQuery(document).ready(function ($) {
    $("select[name=second_currency_id]").on("change", function () {
        var self = this;
        $("select[name=first_currency_id]")
            .find("option")
            .prop("disabled", function () {
                return this.value == self.value;
            });
    });

    $("select[name=first_currency_id]").on("change", function () {
        var self = this;
        $("select[name=second_currency_id]")
            .find("option")
            .prop("disabled", function () {
                return this.value == self.value;
            });
    });
});
// Choose Option Add Rates to Currency Form Currency End

jQuery(document).ready(function ($) {

    // Add Currency Ajax Start
    $("#crc_currency_register").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]); // Use FormData to include file inputs
        var messages = $("#form-messages");
        var actiontype = document.getElementById("action_type_currency").value;
        $.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: formData, // Use FormData object
            dataType: "json",
            contentType: false, // Required when sending FormData
            processData: false, // Required when sending FormData
            success: function (response) {
                if (response.success) {
                    // Clear form fields
                    form.find("input[type=text]").val("");
                    form.find("#currency_logo").val("");
                    $(".file-upload").removeClass("active");
                    $("#noFile").text("No file chosen...");
                    // Show success message
                    messages.html(
                        '<div class="notice notice-success"><p>' +
                        response.data.message +
                        "</p></div>"
                    );
                    setTimeout(function() {
                        messages.html('');
                    }, 2000);
                    // Refresh the table
                    $("table.currencytable tbody").html(response.data.table);
                    // $("table.ratetable tbody").html(response.data.table_2);
                    console.log(response.data);
                    if (actiontype == "crc_update_currency") {
                        $("#action_type_currency").val("crc_add_currency");
                        $("#submit_currency").val("Submit Data");
                    }
                    $("#first_currency_id").html(response.data.Options);
                    $("#second_currency_id").html(response.data.Options);
                } else {
                    // Show error message
                    messages.html(
                        '<div class="notice notice-error"><p>' +
                        response.data.message +
                        "</p></div>"
                    );
                }
            },
            error: function (xhr, status, error) {
                // Show error message
                messages.html(
                    '<div class="notice notice-error"><p>' +
                    xhr.responseText +
                    "</p></div>"
                );
            },
        });
    });
    // Add Currency Ajax End

    // Add Conversion Against Currency Ajax Start
    $("#crc_currency_rate_converter").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        var messages = $("#form-messages-2");
        var actiontype = document.getElementById("action_type_rate").value;
        $.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // Clear form fields
                    // Reset dropdowns in the form by their IDs
                    $("#first_currency_id").val("");
                    $("#second_currency_id").val("");
                    $("#conversion_rate").val("");
                    $("#minimum_amount").val("");
                    $("#platform_fee").val("");
                    $("#currency_redirect_link").val("");

                    messages.html(
                        '<div class="notice notice-success"><p>' +
                        response.data.message +
                        "</p></div>"
                    );
                    setTimeout(function() {
                        messages.html('');
                    }, 2000);
                    // Refresh the table
                    $("table.ratetable tbody").html(response.data.table);
                    if (actiontype == "crc_update_currency_rate") {
                        $("#action_type_rate").val("crc_add_currency_rate");
                        $("#submit_rate").val("Submit Data");
                        $("#currency_id").val("");
                    }
                } else {
                    // Show error message
                    messages.html(
                        '<div class="notice notice-error"><p>' +
                        response.data.message +
                        "</p></div>"
                    );
                }
            },
            error: function (xhr, status, error) {
                // Show error message
                messages.html(
                    '<div class="notice notice-error"><p>' +
                    xhr.responseText +
                    "</p></div>"
                );
            },
        });
    });

    $('.switch').on('click', function() {
        var checkboxName = $(this).prev('input[type="checkbox"]').attr('name');
        var switchState = $(this).prev('input[type="checkbox"]').is(':checked') ? 'off' : 'on';

        // Send AJAX request to update the switch state
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'crc_update_switch_state',
                checkbox_name: checkboxName,
                switch_state: switchState
            },
            success: function(response) {
                if (response.success) {
                    console.log('Switch state updated successfully.');
                } else {
                    console.error('Error updating switch state: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error: ' + error);
            }
        });
    });

});

new DataTable("#crc_form_data", {
    responsive: true,
});

new DataTable("#crc_form_data_2", {
    responsive: true,
});
function edit_rate(id) {

    jQuery.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: {
            action: "fetch_conversion_rate_details",
            rate_id: id,
        },
        success: function (response) {
            jQuery("#conversion_rate").val(response.data.conversion_rate);
            jQuery("#minimum_amount").val(response.data.minimum_amount);
            jQuery("#platform_fee").val(response.data.platform_fee);
            jQuery("#currency_redirect_link").val(response.data.currency_redirect_link);
            jQuery("#first_currency_id").val(response.data.first_currency_id);
            jQuery("#second_currency_id").val(response.data.second_currency_id);
            jQuery("#submit_rate").val("Update Data");
            jQuery("#action_type_rate").val("crc_update_currency_rate");
            jQuery("#crc_currency_rate_converter").find("input:first").focus();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}

function edit_currency(id) {
    
    jQuery.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: {
            action: "fetch_currency_details",
            currency_id: id,
        },
        success: function (response) {
            jQuery("#currency_name").val(response.data.currency_name);
            jQuery("#currency_symbol").val(response.data.currency_symbol);
            jQuery("#currency_id").val(id);
            // Update other form fields as needed
            jQuery("#submit_currency").val("Update Data");
            jQuery("#action_type_currency").val("crc_update_currency");
            jQuery("#crc_currency_register").find("input:first").focus();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}
function delete_currency(id, delete_currency_id) {
    var type = "loader-red";
    var loaderHtml = '<span class="'+type+'"></span>';
    var deleteButtonCurrrency = document.getElementById(delete_currency_id);
    deleteButtonCurrrency.innerHTML = loaderHtml; // Set the loader HTML directly as the button's content

        jQuery.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: {
                action: "delete_currency",
                currency_id: id,
            },
            success: function (response) {
                jQuery("table.currencytable tbody").html(response.data.table);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
}

function delete_rate(id, delete_rate_id) {
    var type = "loader-red";
    var loaderHtml = '<span class="'+type+'"></span>';
    var deleteButtonRate = document.getElementById(delete_rate_id);
    deleteButtonRate.innerHTML = loaderHtml; // Set the loader HTML directly as the button's content
    jQuery.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: {
            action: "delete_conversion_rate",
            rate_id: id,
        },
        success: function (response) {
            jQuery("table.ratetable tbody").html(response.data.table);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}
