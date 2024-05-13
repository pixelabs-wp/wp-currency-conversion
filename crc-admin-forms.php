<?php
//Forms: -- Add Currency--  , -- Add Rates to Currency--  and -- Tables of View -- Start 
function render_crc_currency_rate_converter()
{  
    $link_url = get_option('crc_link_url');
    if(!$link_url)
    {
        $link_url = "";
    }
    $crc_enable  = get_option('crc_enable');
    if (!$crc_enable) {
        $crc_enable = 'off';
        update_option('crc_enable', $crc_enable);
    }
    ?>

    <head>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    </head>
    <h3><?php _e('Currency Rate Exchanger', 'currency_rate_converter'); ?></h3>
    <div id="crc-forms">
       
        <div class="data_form">
            <form id="form">
                <h3><?php _e('Enable / Disable ShortCode', 'currency_rate_converter'); ?></h3>
                <input type="checkbox" id="crc_enable" name="crc_enable" style="display: none !important;" <?php echo ($crc_enable === 'on') ? 'checked' : ''; ?>>
                <label class="switch" for="crc_enable"></label>
            </form>
        </div>
    </div>
    <div id="crc-forms">
        <div id="crc_currency_addition_form">
            <h2><?php _e('Add Currency', 'currency_rate_converter'); ?></h2>
            <div id="form-messages"></div>
            <form id="crc_currency_register" action="" method="POST" enctype="multipart/form-data">
                <p class="form-items">
                    <label for="currency_name"><?php _e('Currency Name', 'currency_rate_converter'); ?></label>
                    <input type="text" id="currency_name" name="currency_name" required>
                </p>
                <p class="form-items">
                    <label for="currency_symbol"><?php _e('Currency Symbol', 'currency_rate_converter'); ?></label>
                    <input type="text" id="currency_symbol" name="currency_symbol" required>
                </p>
                <div class="file-upload form-items">
                    <label for="" style="text-align: left;"><?php _e('Choose Logo', 'currency_rate_converter'); ?></label>
                    <div class="file-select">
                        <div class="file-select-button" id="fileName"><?php _e('Choose File', 'currency_rate_converter'); ?></div>
                        <div class="file-select-name" id="noFile"><?php _e('No file chosen...', 'currency_rate_converter'); ?></div>
                        <input type="file" id="currency_logo" name="currency_logo">
                    </div>
                </div>

                <p class="form-button">
                    <input type="hidden" name="currency_id" id="currency_id" value="">
                    <input type="hidden" name="action" id="action_type_currency" value="crc_add_currency">
                    <?php wp_nonce_field('crc_add_currency', 'crc_add_currency_nonce'); ?>
                    <input type="submit" value="<?php _e('Submit Data', 'currency_rate_converter'); ?>" id="submit_currency" class="btn">
                </p>
            </form>
        </div>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'crc_currency_listing'; // Use dynamic table prefix
        $currencies = $wpdb->get_results("SELECT id, currency_name, currency_symbol  FROM $table_name");
        if ($currencies) {
        ?>
            <!-- HTML form for adding Currency Rates -->
            <div id="crc_currency_rates_form">
                <h2><?php _e('Add Conversion Rates', 'currency_rate_converter'); ?></h2>
                <div id="form-messages-2"></div>
                <form id="crc_currency_rate_converter" action="" method="POST">

                    <div class="form-inline-wrap">
                        <p style="width: 50%;"><label><?php _e('First Currency', 'currency_rate_converter'); ?></label>
                            <select name="first_currency_id" id="first_currency_id" required>
                                <option value=""><?php _e('Select Currency', 'currency_rate_converter'); ?></option>
                                <?php foreach ($currencies as $currency) { ?>
                                    <option value="<?php echo $currency->id; ?>">
                                        <?php echo $currency->currency_name; ?> - <?php echo $currency->currency_symbol; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </p>
                        <p>
                            <label><?php _e('Amount', 'currency_rate_converter'); ?></label>
                            <input type="number" id="first_currency_rate" value="1" name="first_currency_rate" readonly required>
                        </p>
                    </div>
                    <div class="form-inline-wrap">
                        <p><label><?php _e('Second Currency', 'currency_rate_converter'); ?></label>
                            <select name="second_currency_id" id="second_currency_id" required>
                                <option value=""><?php _e('Select Currency', 'currency_rate_converter'); ?></option>
                                <?php foreach ($currencies as $currency) { ?>
                                    <option value="<?php echo $currency->id; ?>">
                                        <?php echo $currency->currency_name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </p>
                        <p><label><?php _e('Convert Rate', 'currency_rate_converter'); ?></label>
                            <input type="number" placeholder="0" required id="conversion_rate" name="conversion_rate" step="0.00001" value="" title="Currency">
                        </p>
                    </div>

                    <div class="form-inline-wrap">
                        <p>
                            <label><?php _e('Platform Fee', 'currency_rate_converter'); ?></label>
                            <input type="number" placeholder="0" required id="platform_fee" name="platform_fee" step="0.00001" value="" title="Currency">
                        </p>
                        <p>
                            <label><?php _e('Minimum Amount', 'currency_rate_converter'); ?></label>
                            <input type="number" placeholder="0" required id="minimum_amount" name="minimum_amount" step="0.00001" value="" title="Currency">
                        </p>
                    </div>
                    <div class="">
                        <p>
                            <label><?php _e('Currency Redirect Link', 'currency_rate_converter'); ?></label>
                            <input type="text" required id="currency_redirect_link" name="currency_redirect_link" value="" title="currency_redirect_link">
                        </p>
                    </div>
                    <input type="hidden" name="action" id="action_type_rate" value="crc_add_currency_rate">
                    <?php wp_nonce_field('crc_add_currency_rate', 'crc_add_currency_rate_nonce'); ?>
                    <p>
                        <input type="submit" value="<?php _e('Submit Data', 'currency_rate_converter'); ?>" id="submit_rate" class="btn">
                    </p>
                </form>
            </div>
        <?php } ?>
    </div>
    
    <div class="wrap">
        <h3><?php _e('Currencies', 'currency_rate_converter'); ?></h3>
        <table id="crc_form_data_2" class="display nowrap currencytable">
            <thead>
                <tr>
                    <th><?php _e('Logo', 'currency_rate_converter'); ?></th>
                    <th><?php _e('Symbol', 'currency_rate_converter'); ?></th>
                    <th><?php _e('Name', 'currency_rate_converter'); ?></th>
                    <th><?php _e('Action', 'currency_rate_converter'); ?></th> <!-- New column for actions -->
                </tr>
            </thead>
            <tbody>
                <?php
                $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_listing ORDER BY currency_created_at DESC");
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
                ?>
            </tbody>
        </table>
    </div>
    <div class="wrap">
        <h3><?php _e('Converstion Rates', 'currency_rate_converter'); ?></h3>
        <table id="crc_form_data" class="display nowrap ratetable">
            <thead>
                <tr>
                    <th><?php _e('Coin', 'currency_rate_converter'); ?></th>
                    <th><?php _e('Rate per 1', 'currency_rate_converter'); ?></th>
                    <th><?php _e('Platform Fee', 'currency_rate_converter'); ?></th>
                    <th><?php _e('Minimum Amount', 'currency_rate_converter'); ?></th>
                    <th><?php _e('Link', 'currency_rate_converter'); ?></th>
                    <th><?php _e('Action', 'currency_rate_converter'); ?></th> <!-- New column for actions -->
                </tr>
            </thead>
            <tbody>
                <?php
                $rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}crc_currency_rates ORDER BY rates_created_at DESC");
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
                ?>
            </tbody>
        </table>
    </div>



<?php

}

//Forms: -- Add Currency--  , -- Add Rates to Currency--  and -- Tables of View --  End

?>
