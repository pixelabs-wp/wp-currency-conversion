<?php

/**
 * Plugin Name: Currency Rate Converter
 * Plugin URI: https://github.com/Wasif-kn/wordpress-form-submissions
 * Description: Shows exchange rates of the currency
 * Version: 1.0
 * Author: PixelLabs Technologies
 * Author URI: https://www.linkedin.com/company/pixelabs-tech/
 * License: GPL-2.0+
 * Text Domain: currency_rate_converter
 * Domain Path: /languages
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Plugin Root File
define('CURRENCY_CONVERTER_PLUGIN_FILE',    __FILE__);

// Plugin Folder Path
define('CURRENCY_CONVERTER_PLUGIN_DIR',    plugin_dir_path(CURRENCY_CONVERTER_PLUGIN_FILE));

define('CURRENCY_CONVERTER_PLUGIN_URL',    plugin_dir_url(CURRENCY_CONVERTER_PLUGIN_FILE));

include 'crc-admin-forms.php';
include 'crc-shortcode.php';

//Enque Js File
function add_currency_script_to_menu_page()
{

    // Check for existing jQuery first
    if (!wp_script_is('jquery', 'registered')) {
        // Register the latest jQuery from the Google CDN
        wp_deregister_script('jquery');
        wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js', array(), '3.6.4', true);
    }
    // Localize the script
    $script_data_array = array(
        'ajax_url' => admin_url('admin-ajax.php'),
    );

    // loading Admin page js

    wp_register_script('crc-admin-script', CURRENCY_CONVERTER_PLUGIN_URL . 'js/crc-admin.js', array('jquery'), false, true);
    wp_localize_script('crc-admin-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_script('crc-admin-script');
    // loading Shortcode js
    wp_register_script('crc-shortcode-script', CURRENCY_CONVERTER_PLUGIN_URL . 'js/crc-shortcode-script.js', array('jquery'), false, true);
    wp_enqueue_script('crc-shortcode-script');

    wp_enqueue_style('crc-style', CURRENCY_CONVERTER_PLUGIN_URL . 'style/crc-style.css', false, '1.0', 'all'); // Inside a plugin

}
add_action('admin_enqueue_scripts', 'add_currency_script_to_menu_page');

// Function to register a menu page in WordPress admin
function register_crc_currency_rate_converter()
{
    add_menu_page(
        'Currency Rate Converter',
        'Currency Rate Converter',
        'manage_options',
        'crc_currency_rate_converter',
        'render_crc_currency_rate_converter',
        'dashicons-money-alt',
        90
    );
}

// Add action to create the menu page
add_action('admin_menu', 'register_crc_currency_rate_converter');

// Register plugin activation hook
register_activation_hook(__FILE__, 'crc_activate_currency_listing_table');
register_activation_hook(__FILE__, 'crc_activate_currency_rates_table');
register_uninstall_hook(__FILE__, 'crc_delete_currency');



function crc_activate_currency_listing_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'crc_currency_listing';


    // Check if the table already exists in the database
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        return; // Table already exists, no need to create a new table
    }

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        currency_name varchar(100) NOT NULL,
        currency_logo varchar(100) NOT NULL,
        currency_symbol varchar(100) NOT NULL,
        currency_created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $result = $wpdb->query($sql);

    if ($result === false) {
        error_log("Error creating table: " . $wpdb->last_error);
    }
}

function crc_activate_currency_rates_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'crc_currency_rates';


    // Check if the table already exists in the database
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        return; // Table already exists, no need to create a new table
    }

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        first_currency_id mediumint(9) NOT NULL,
        second_currency_id mediumint(9) NOT NULL,
        conversion_rate varchar(100) NOT NULL,
        minimum_amount varchar(100) NOT NULL,
        platform_fee varchar(100) NOT NULL,
        currency_redirect_link varchar(100) NOT NULL,
        rates_created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $result = $wpdb->query($sql);

    if ($result === false) {
        error_log("Error creating table: " . $wpdb->last_error);
    }
}

// Load plugin text domain for translation
add_action('plugins_loaded', 'crc_load_textdomain');
function crc_load_textdomain() {
    
}

function crc_delete_currency()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'crc_currency_listing';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    $table_name = $wpdb->prefix . 'crc_currency_rates';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");

}
// -- Add Currency -- Ajax Handler Start
function crc_add_currency()
{
    check_ajax_referer('crc_add_currency', 'crc_add_currency_nonce');

    global $wpdb;

    $table_name = $wpdb->prefix . 'crc_currency_listing';
    $currency_name = $_POST['currency_name'];
    $currency_symbol = $_POST['currency_symbol'];
    $upload_dir = wp_upload_dir();

    // Create the directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle file upload
    $uploadedfile = wp_handle_upload($_FILES["currency_logo"], array('upload_dir' => $upload_dir, 'test_form' => false));

    // Check for file upload errors
    if (isset($uploadedfile['error']) && $uploadedfile['error']) {
        wp_die('Error uploading file: ' . $uploadedfile['error']);
    }

    $file_url = $uploadedfile['url'];

    $result = $wpdb->insert(
        $table_name,
        array(
            'currency_name' => $currency_name,
            'currency_logo' => $file_url,
            'currency_symbol' => $currency_symbol,
        ),
        array(
            '%s',
            '%s',
            '%s',
        )
    );

    if ($result) {
        $message = __('Currency added successfully.', 'currency_rate_converter');
        $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing ORDER BY currency_created_at DESC");
        ob_start();
        foreach ($rates as $rate) {
            echo "<tr>";
            echo "<td><img style='width: 30px;' src='{$rate->currency_logo}'></td>";
            echo "<td>{$rate->currency_symbol}</td>";
            echo "<td>{$rate->currency_name}</td>";
            echo "<td>
                    <div class='crc_action_div' >
                        <button class='edit-currency' onclick='edit_currency({$rate->id})'>
                            <span class='dashicons dashicons-edit'></span>
                        </button>
                        <button class='delete-currency' onclick='delete_currency({$rate->id}, `delete_currency_{$rate->id}`)' id='delete_currency_{$rate->id}'>
                            <span class='dashicons dashicons-trash'></span>
                        </button>
                    </div>  
                    </td>";
            echo "</tr>";
        }
        $table = ob_get_clean();

        $table_name = $wpdb->prefix . 'crc_currency_listing';
        $Options = $wpdb->get_results("SELECT id, currency_name, currency_symbol  FROM $table_name");
        ob_start();
        echo "<option value=''>Select Currency</option>";
        foreach ($Options as $Option) {
            echo " <option value='{$Option->id}'>{$Option->currency_name} - {$Option->currency_symbol}</option>";
        }
        $Options = ob_get_clean();
        wp_send_json_success(array('message' => $message, 'table' => $table, 'Options' => $Options));
    } else {
        wp_send_json_error(array('message' => __('Failed to add currency rate.', 'currency_rate_converter')));
    }

    wp_die();
}
add_action('wp_ajax_crc_add_currency', 'crc_add_currency');
add_action('wp_ajax_nopriv_crc_add_currency', 'crc_add_currency');
// -- Add Currency -- Ajax Handler End


// -- Add Currency -- Ajax Handler Start
function crc_update_currency()
{
    check_ajax_referer('crc_add_currency', 'crc_add_currency_nonce');

    global $wpdb;

    $table_name = $wpdb->prefix . 'crc_currency_listing';
    $currency_name = $_POST['currency_name'];
    $currency_symbol = $_POST['currency_symbol'];
    $currency_id = $_POST['currency_id'];
    $array = $_FILES["currency_logo"];

    if ( $file_name = $_FILES["currency_logo"]["name"] ) {
        $upload_dir = wp_upload_dir();

        // Create the directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Handle file upload
        $uploadedfile = wp_handle_upload($_FILES["currency_logo"], array('upload_dir' => $upload_dir, 'test_form' => false));

        // Check for file upload errors
        if (isset($uploadedfile['error']) && $uploadedfile['error']) {
            wp_die('Error uploading file: ' . $uploadedfile['error']);
        }

        $file_url = $uploadedfile['url'];
        $previous_logo_url = $wpdb->get_var(
            $wpdb->prepare("SELECT currency_logo FROM $table_name WHERE id = %d", $currency_id)
        );
        $result = $wpdb->update(
            $table_name,
            array(
                'currency_name' => $currency_name,
                'currency_logo' => $file_url,
                'currency_symbol' => $currency_symbol,
            ),
            array('id' => $currency_id), // Update based on currency ID
            array(
                '%s',
                '%s',
                '%s'
            ),
            array('%d') // Ensure $currency_id is treated as an integer
        );


        if ($result) {
            if (!empty($previous_logo_url)) {
                // Construct the path to the previous logo file
                $previous_logo_path = $upload_dir . basename($previous_logo_url);

                // Check if the previous logo file exists and delete it
                if (file_exists($previous_logo_path)) {
                    unlink($previous_logo_path); // Delete the file
                }
            }
            $message = __('Currency Updated successfully.', 'currency_rate_converter');
            $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing ORDER BY currency_created_at DESC");
            ob_start();
            foreach ($rates as $rate) {
                echo "<tr>";
                echo "<td><img style='width: 30px;' src='{$rate->currency_logo}'></td>";
                echo "<td>{$rate->currency_symbol}</td>";
                echo "<td>{$rate->currency_name}</td>";
                echo "<td>
                    <div class='crc_action_div' >
                        <button class='edit-currency' onclick='edit_currency({$rate->id})'>
                            <span class='dashicons dashicons-edit'></span>
                        </button>
                        <button class='delete-currency' onclick='delete_currency({$rate->id}, `delete_currency_{$rate->id}`)' id='delete_currency_{$rate->id}'>
                            <span class='dashicons dashicons-trash'></span>
                        </button>
                    </div>  
                </td>";
        echo "</tr>";
            }
            $table = ob_get_clean();
            wp_send_json_success(array('message' => $message, 'table' => $table));
        } else {
            wp_send_json_error(array('message' => __('Failed to add currency rate.', 'currency_rate_converter')));
        }

        wp_die();
    } else {


        $result = $wpdb->update(
            $table_name,
            array(
                'currency_name' => $currency_name,
                'currency_symbol' => $currency_symbol
            ),
            array('id' => $currency_id), // Update based on currency ID
            array('%s', '%s'),
            array('%d')
        );

        if ($result !== false) {
        // Currency updated successfully
        $message = __('Currency Updated successfully.', 'currency_rate_converter');
        // Retrieve updated currency listing
        $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing ORDER BY currency_created_at DESC");
        // Build HTML table for updated currency listing
        ob_start();
        foreach ($rates as $rate) {
            echo "<tr>";
            echo "<td><img style='width: 30px;' src='{$rate->currency_logo}'></td>";
            echo "<td>{$rate->currency_symbol}</td>";
            echo "<td>{$rate->currency_name}</td>";
            echo "<td>
                    <div class='crc_action_div' >
                        <button class='edit-currency' onclick='edit_currency({$rate->id})'>
                            <span class='dashicons dashicons-edit'></span>
                        </button>
                        <button class='delete-currency' onclick='delete_currency({$rate->id}, `delete_currency_{$rate->id}`)' id='delete_currency_{$rate->id}'>
                            <span class='dashicons dashicons-trash'></span>
                        </button>
                    </div>  
                </td>";
            echo "</tr>";
        }
        $table = ob_get_clean();
        wp_send_json_success(array('message' => $message, 'table' => $table));
    } else {
        // Error updating currency
        wp_send_json_error(array('message' => __('Failed to update currency rate.', 'currency_rate_converter')));
    }

        wp_die();
    }
}
add_action('wp_ajax_crc_update_currency', 'crc_update_currency');
add_action('wp_ajax_nopriv_crc_update_currency', 'crc_update_currency');
// -- Add Currency -- Ajax Handler End


// -- Add Rates to Currency -- Ajax Handler Start
function crc_add_currency_rate()
{
    check_ajax_referer('crc_add_currency_rate', 'crc_add_currency_rate_nonce');

    global $wpdb;

    $table_name = $wpdb->prefix . 'crc_currency_rates';
    $first_currency_id = $_POST['first_currency_id'];
    $second_currency_id = $_POST['second_currency_id'];
    $minimum_amount = $_POST['minimum_amount'];
    $conversion_rate = $_POST['conversion_rate'];
    $platform_fee = $_POST['platform_fee'];
    $currency_redirect_link = $_POST['currency_redirect_link'];

    $result = $wpdb->insert(
        $table_name,
        array(
            'first_currency_id' => $first_currency_id,
            'second_currency_id' => $second_currency_id,
            'conversion_rate' => $conversion_rate,
            'minimum_amount' => $minimum_amount,
            'platform_fee' => $platform_fee,
            'currency_redirect_link' => $currency_redirect_link
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );

    if ($result) {
        $message = __('Currency rate added successfully.', 'currency_rate_converter');
        $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_rates ORDER BY rates_created_at DESC");
        ob_start();
        foreach ($rates as $rate) {
            $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing where id={$rate->first_currency_id}");
            $currency_name = $data[0]->currency_name;
            $data_2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing where id={$rate->second_currency_id}");
            $currency_name_2 = $data_2[0]->currency_name;
            $conversion_rate = $rate->conversion_rate;
            $minimum_amount = $rate->minimum_amount;
            $platform_fee = $rate->platform_fee;
            $currency_redirect_link = $rate->currency_redirect_link;
            echo "<tr>";
            echo "<td>{$currency_name} -> {$currency_name_2}</td>";
            echo "<td>{$conversion_rate}</td>";
            echo "<td>{$platform_fee}</td>";
            echo "<td>{$minimum_amount}</td>";
            echo "<td>{$currency_redirect_link}</td>";
            echo "<td>
                            <div class='crc_action_div' >
                                <button class='edit-rate' onclick='edit_rate({$rate->id})'  >
                                    <span class='dashicons dashicons-edit'></span>
                                </button> 
                                <button class='delete-rate' onclick='delete_rate({$rate->id}, `delete_rate_{$rate->id}`)' id='delete_rate_{$rate->id}'>
                                    <span class='dashicons dashicons-trash'></span>
                                </button>
                            </div>
                        </td>";
                    echo "</tr>";
        }
        $table = ob_get_clean();
        wp_send_json_success(array('message' => $message, 'table' => $table));
    } else {
        wp_send_json_error(array('message' => __('Failed to add currency rate.', 'currency_rate_converter')));
    }

    wp_die();
}
add_action('wp_ajax_crc_add_currency_rate', 'crc_add_currency_rate');
add_action('wp_ajax_nopriv_crc_add_currency_rate', 'crc_add_currency_rate');
// -- Add Rates to Currency -- Ajax Handler End


// -- Add Rates to Currency -- Ajax Handler Start
function crc_update_currency_rate()
{

    global $wpdb;

    $table_name = $wpdb->prefix . 'crc_currency_rates';
    $first_currency_id = $_POST['first_currency_id'];
    $second_currency_id = $_POST['second_currency_id'];
    $conversion_rate = $_POST['conversion_rate'];
    $minimum_amount = $_POST['minimum_amount'];
    $platform_fee = $_POST['platform_fee'];
    $currency_redirect_link = $_POST['currency_redirect_link'];

    $result = $wpdb->update(
        $table_name,
        array(
            'conversion_rate' => $conversion_rate,
            'minimum_amount' => $minimum_amount,
            'platform_fee' => $platform_fee,
            'currency_redirect_link' => $currency_redirect_link
            
        ),
        array(
            'first_currency_id' => $first_currency_id,
            'second_currency_id' => $second_currency_id
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s'
        ),
        array(
            '%s',
            '%s'
        )
    );

    if ($result) {
        $message = __('Currency rate Updated successfully.', 'currency_rate_converter');
        $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_rates ORDER BY rates_created_at DESC");
        ob_start();
        foreach ($rates as $rate) {
            $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing where id={$rate->first_currency_id}");
            $currency_name = $data[0]->currency_name;
            $data_2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing where id={$rate->second_currency_id}");
            $currency_name_2 = $data_2[0]->currency_name;
            $conversion_rate = $rate->conversion_rate;
            $minimum_amount = $rate->minimum_amount;
            $platform_fee = $rate->platform_fee;
            $currency_redirect_link = $rate->currency_redirect_link;
            echo "<tr>";
            echo "<td>{$currency_name} -> {$currency_name_2}</td>";
            echo "<td>{$conversion_rate}</td>";
            echo "<td>{$platform_fee}</td>";
            echo "<td>{$minimum_amount}</td>";
            echo "<td>{$currency_redirect_link}</td>";
            echo "<td>
                            <div class='crc_action_div' >
                                <button class='edit-rate' onclick='edit_rate({$rate->id})'  >
                                    <span class='dashicons dashicons-edit'></span>
                                </button> 
                                <button class='delete-rate' onclick='delete_rate({$rate->id}, `delete_rate_{$rate->id}`)' id='delete_rate_{$rate->id}'>
                                    <span class='dashicons dashicons-trash'></span>
                                </button>
                            </div>
                        </td>";
                    echo "</tr>";
        }
        $table = ob_get_clean();
        wp_send_json_success(array('message' => $message, 'table' => $table));
    } else {
        wp_send_json_error(array('message' => __('Failed to add currency rate.', 'currency_rate_converter')));
    }

    wp_die();
}
add_action('wp_ajax_crc_update_currency_rate', 'crc_update_currency_rate');
add_action('wp_ajax_nopriv_crc_update_currency_rate', 'crc_update_currency_rate');




function crc_get_currency_conversions()
{

    global $wpdb;

    // Get the left currency ID from the AJAX request
    $left_currency_id = $_POST['left_currency_id'];

    // Query the database for conversions where first_currency_id matches left_currency_id
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT second_currency_id FROM {$wpdb->prefix}crc_currency_rates WHERE first_currency_id = %d",
            $left_currency_id
        )
    );

    // Prepare an array of available second currency IDs
    $available_second_currency_ids = array();
    foreach ($results as $result) {
        $available_second_currency_ids[] = $result->second_currency_id;
    }

    // Send the available second currency IDs as the AJAX response
    wp_send_json_success($available_second_currency_ids);
}
add_action('wp_ajax_crc_get_currency_conversions', 'crc_get_currency_conversions');
add_action('wp_ajax_nopriv_crc_get_currency_conversions', 'crc_get_currency_conversions'); // Allow non-logged in users to access the AJAX action


function crc_switch_currency_conversions()
{

    global $wpdb;

    // Get the left currency ID from the AJAX request
    $left_currency_id = $_POST['left_currency_id'];
    $right_currency_id = $_POST['right_currency_id'];
    $type = $_POST['type'];
    if ($type == "switch") {
        // Query the database for conversions where first_currency_id matches left_currency_id
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}crc_currency_rates WHERE first_currency_id = %d AND second_currency_id = %d",
                $right_currency_id,
                $left_currency_id
            )
        );
    } else if ($type == "select") {
        // Query the database for conversions where first_currency_id matches left_currency_id
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}crc_currency_rates WHERE first_currency_id = %d AND second_currency_id = %d",
                $left_currency_id,
                $right_currency_id
            )
        );
    }

    // Check if any rows are returned
    if (!empty($result)) {
        // Conversion rate exists, send success response
        $response = array(
            'id' => $result[0]->id, // Include the ID from the first row of the result
            'conversion_rate' => $result[0]->conversion_rate, // Include the ID from the first row of the result
            'minimum_amount' => $result[0]->minimum_amount,
            'platform_fee' => $result[0]->platform_fee,
            'currency_redirect_link' => $result[0]->currency_redirect_link // Include the ID from the first row of the result
        );
        wp_send_json_success($response);
    } else {
        // No conversion rate found, send failure response
        wp_send_json_error($result);
    }
}
add_action('wp_ajax_crc_switch_currency_conversions', 'crc_switch_currency_conversions');
add_action('wp_ajax_nopriv_crc_switch_currency_conversions', 'crc_switch_currency_conversions'); // Allow non-logged in users to access the AJAX action



// Fetch currency details AJAX handler
function fetch_currency_details()
{
    $currency_id = $_POST['currency_id'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'crc_currency_listing';
    $currency_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $currency_id), ARRAY_A);

    wp_send_json_success($currency_details);
}
add_action('wp_ajax_fetch_currency_details', 'fetch_currency_details');

// Delete currency AJAX handler
function delete_currency()
{
    $currency_id = $_POST['currency_id'];

    global $wpdb;
    

    $table_name = $wpdb->prefix . 'crc_currency_listing';
    $result = $wpdb->delete($table_name, array('id' => $currency_id));

    if ($result) {
    
        $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing ORDER BY currency_created_at DESC");
        $table = "";
        foreach ($rates as $rate) {
            $table = $table. "<tr>
            <td><img style='width: 30px;' src='{$rate->currency_logo}'></td>
            <td>{$rate->currency_symbol}</td>
            <td>{$rate->currency_name}</td>
            <td>
                    <div class='crc_action_div' >
                        <button class='edit-currency' onclick='edit_currency({$rate->id})'>
                            <span class='dashicons dashicons-edit'></span>
                        </button>
                        <button class='delete-currency' onclick='delete_currency({$rate->id}, `delete_currency_{$rate->id}`)' id='delete_currency_{$rate->id}'>
                            <span class='dashicons dashicons-trash'></span>
                        </button>
                    </div>  
                    </td>
            </tr>";
        }
        $table_name = $wpdb->prefix . 'crc_currency_rates';
        $wpdb->delete($table_name, array('first_currency_id' => $currency_id));
        $wpdb->delete($table_name, array('second_currency_id' => $currency_id));
        
        wp_send_json_success(array('table' => $table, 'table_2' => ""));
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_delete_currency', 'delete_currency');

// Fetch conversion rate details AJAX handler
function fetch_conversion_rate_details()
{
    $rate_id = $_POST['rate_id'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'crc_currency_rates';
    $conversion_rate_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $rate_id), ARRAY_A);

    wp_send_json_success($conversion_rate_details);
}
add_action('wp_ajax_fetch_conversion_rate_details', 'fetch_conversion_rate_details');

// Delete conversion rate AJAX handler
function delete_conversion_rate()
{
    $rate_id = $_POST['rate_id'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'crc_currency_rates';
    $result = $wpdb->delete($table_name, array('id' => $rate_id));

    if ($result) {
        $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_rates ORDER BY rates_created_at DESC");
        ob_start();
        foreach ($rates as $rate) {
            $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing where id={$rate->first_currency_id}");
            $currency_name = $data[0]->currency_name;
            $data_2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing where id={$rate->second_currency_id}");
            $currency_name_2 = $data_2[0]->currency_name;
            $conversion_rate = $rate->conversion_rate;
            $minimum_amount = $rate->minimum_amount;
            $platform_fee = $rate->platform_fee;
            $currency_redirect_link = $rate->currency_redirect_link;
            echo "<tr>";
            echo "<td>{$currency_name} -> {$currency_name_2}</td>";
            echo "<td>{$conversion_rate}</td>";
            echo "<td>{$platform_fee}</td>";
            echo "<td>{$minimum_amount}</td>";
            echo "<td>{$currency_redirect_link}</td>";
            echo "<td>
            <div class='crc_action_div' >
                <button class='edit-rate' onclick='edit_rate({$rate->id})'  >
                    <span class='dashicons dashicons-edit'></span>
                </button> 
                <button class='delete-rate' onclick='delete_rate({$rate->id}, `delete_rate_{$rate->id}`)' id='delete_rate_{$rate->id}'>
                    <span class='dashicons dashicons-trash'></span>
                </button>
            </div>
        </td>";
    echo "</tr>";
        }
        $table = ob_get_clean();
        wp_send_json_success(array('table' => $table));
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_delete_conversion_rate', 'delete_conversion_rate');


// WordPress AJAX action hook for updating switch state
add_action('wp_ajax_crc_update_switch_state', 'crc_update_switch_state');

function crc_update_switch_state()
{
    // Check if the request is coming from a logged-in user
    if (is_user_logged_in()) {
        // Retrieve the switch state and checkbox name from the AJAX request
        $switch_state = isset($_POST['switch_state']) ? sanitize_text_field($_POST['switch_state']) : '';
        $checkbox_name = isset($_POST['checkbox_name']) ? sanitize_text_field($_POST['checkbox_name']) : '';

        // Update the option in the WordPress database
        update_option($checkbox_name, $switch_state);

        // Return a response
        echo json_encode(array('success' => true));
    } else {
        // Return an error response if the user is not logged in
        echo json_encode(array('success' => false, 'message' => 'User is not logged in.'));
    }

    // Always exit to avoid further execution
    wp_die();
}