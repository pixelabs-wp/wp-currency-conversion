<?php

//Shortcode Render Start
function crc_currency_exchange_function()
{
    wp_register_script('crc-shortcode-script', CURRENCY_CONVERTER_PLUGIN_URL . 'js/crc-shortcode-script.js', array('jquery'), false, true);
    wp_enqueue_script('crc-shortcode-script');
    wp_localize_script('crc-shortcode-script', 'crc_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    wp_enqueue_style('crc-style', CURRENCY_CONVERTER_PLUGIN_URL . 'style/crc-style.css', false, '1.0', 'all'); // Inside a plugin

    $link_url = get_option('crc_link_url');
    if (!$link_url) {
        $link_url = "";
    }
    global $wpdb;
    $table_name = $wpdb->prefix . 'crc_currency_listing';
    $currencies = $wpdb->get_results("SELECT * FROM $table_name");
    $crc_enable  = get_option('crc_enable');
    if (!$crc_enable) {
        $crc_enable = 'off';
        update_option('crc_enable', $crc_enable);
    }

    if ($crc_enable == 'off') {
        echo ' <div data-v-4e76d53a="" class="col-lg-10">
       <h3 style="text-align: center;">' . __('Under Maintenance', 'currency_rate_converter') . '</h3>
       </div>';
    } else {

?>
        <div class="sidebar sidebar-left" id="sidebar-left">
            <div style="display: grid; grid-template-columns: 20% 60% 20%; display:none; margin-bottom: 20px;" id="crc-close-btn-left">
                <span style="width: 20px; display: flex; padding: 5px 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480V396.4c0-4 1.5-7.8 4.2-10.7L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z" />
                    </svg>
                </span>
                <span style="padding: 5px 15px; text-align:center; display: inline-block" class="head_hover"><?php _e('Send', 'currency_rate_converter'); ?></span>
                <div style="display: flex; justify-content: center;">
                    <button class="crc-close-btn" onclick="toggleSidebar_left()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="14px">
                            <path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z" />
                        </svg>
                    </button>
                </div>
            </div>
            <div id="q-item-left" style=" flex-direction: column; display: none;">
                <?php foreach ($currencies as $currency) : ?>
                    <div class="currency-option" data-currency-id="<?php echo $currency->id; ?>" data-side="left" onclick="handleCurrencyOptionClick( '<?php echo $currency->currency_symbol; ?>', 'left-currency-id', 'vfrv',<?php echo $currency->id; ?>, '<?php echo $currency->currency_name; ?>', '<?php echo $currency->currency_logo; ?>', 'left')" style="">
                        <span><?php echo $currency->currency_symbol; ?></span>
                        <span class="logo_name_div">
                            <span style="margin-right: 10px"><?php echo $currency->currency_name; ?></span>
                            <span>
                                <img width="20px" style="border-radius: 50%;" src="<?php echo $currency->currency_logo; ?>" alt="<?php echo $currency->currency_name; ?>">
                            </span>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="sidebar sidebar-right" id="sidebar-right">
            <div style="display: grid; grid-template-columns: 20% 60% 20%; display:none; margin-bottom: 20px;" id="crc-close-btn-right">
                <div style="display: flex; justify-content: center;">
                    <button class="crc-close-btn" onclick="toggleSidebar_right()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="14px">
                            <path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z" />
                        </svg>
                    </button>
                </div>
                <span style="padding: 5px 15px; text-align:center; display: inline-block" class="head_hover"><?php _e('Receive', 'currency_rate_converter'); ?></span>
                <span style="width: 20px; display: flex; padding: 5px 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="50px">
                        <path d="M205 34.8c11.5 5.1 19 16.6 19 29.2v64H336c97.2 0 176 78.8 176 176c0 113.3-81.5 163.9-100.2 174.1c-2.5 1.4-5.3 1.9-8.1 1.9c-10.9 0-19.7-8.9-19.7-19.7c0-7.5 4.3-14.4 9.8-19.5c9.4-8.8 22.2-26.4 22.2-56.7c0-53-43-96-96-96H224v64c0 12.6-7.4 24.1-19 29.2s-25 3-34.4-5.4l-160-144C3.9 225.7 0 217.1 0 208s3.9-17.7 10.6-23.8l160-144c9.4-8.5 22.9-10.6 34.4-5.4z" />
                    </svg>
                </span>
            </div>
            <div id="q-item-right" style=" flex-direction: column; display: none;">
                <?php foreach ($currencies as $currency) : ?>
                    <div class="currency-option disabled" data-currency-id="<?php echo $currency->id; ?>" data-side="right" onclick="handleCurrencyOptionClick( '<?php echo $currency->currency_symbol; ?>','right-currency-id', 'vtov',<?php echo $currency->id; ?>, '<?php echo $currency->currency_name; ?>', '<?php echo $currency->currency_logo; ?>', 'right')">
                        <span><?php echo $currency->currency_symbol; ?></span>
                        <span class="logo_name_div">
                            <span style="margin-right: 10px"><?php echo $currency->currency_name; ?></span>
                            <span>
                                <img width="20px" style="border-radius: 50%;" src="<?php echo $currency->currency_logo; ?>" alt="<?php echo $currency->currency_name; ?>">
                            </span>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div data-v-4e76d53a="" class="col-lg-10">
            <div data-v-4e76d53a="" class="home-banner__body">
                <div data-v-4e76d53a="" class="home-banner__block">
                    <div data-v-4e76d53a="" class="home-banner__block-header">
                        <h3 data-v-4e76d53a="" class="home-banner__block-header-title"><?php _e('You send', 'currency_rate_converter'); ?></h3>
                        <span data-v-4e76d53a="" class="home-banner__block-header-description"><?php _e('Min:', 'currency_rate_converter'); ?>&nbsp; <span id="vmin">0</span></span>
                    </div>
                    <div data-v-4e76d53a="" class="home-banner__block-body form-group">
                        <div data-v-4e76d53a="" class="dropdown">
                            <div data-v-4e76d53a="" class="form-field">
                                <input data-v-4e76d53a="" type="number" id="give_val" class="form-control form-control--value f-input" min="0" style="color: white; background: transparent; font-weight: bold; font-size: 16px;">
                                <span id="currency_symbol_left">-</span>
                            </div>
                            <button data-v-4e76d53a="" id="toggle_left_button" onclick="toggleSidebar_left()" class="btn">
                                <div data-v-4e76d53a="" id="vfrv" data-group="ps">
                                    <span data-v-4e76d53a="" id="give_currency" class="name"><?php _e('Select Currency', 'currency_rate_converter'); ?></span>
                                </div>
                                <svg data-v-4e76d53a="" class="ml-auto" width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path data-v-4e76d53a="" fill-rule="evenodd" clip-rule="evenodd" d="M.782.12a.44.44 0 01.637.034L5.497 4.85 9.575.154a.44.44 0 01.637-.034.48.48 0 01.035.661L5.849 5.846A.441.441 0 015.497 6a.441.441 0 01-.352-.154L.747.781a.48.48 0 01.035-.66z" fill="#484D56"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button data-v-4e76d53a="" type="button" id="swap_currencies" class="home-banner__button crc_disabled">
                    <svg data-v-4e76d53a="" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path data-v-4e76d53a="" d="M26 14c0 6.627-5.373 12-12 12-4.4 0-8.246-2.368-10.335-5.898M2 14C2 7.373 7.373 2 14 2c4.483 0 8.393 2.459 10.453 6.102m-20.788 12l-.851 5.186m.85-5.186h5.353m15.436-12l-4.758-.407m4.758.407l.835-5.187" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
                <div data-v-4e76d53a="" class="home-banner__block">
                    <div data-v-4e76d53a="" class="home-banner__block-header">
                        <h3 data-v-4e76d53a="" class="home-banner__block-header-title"><?php _e('You get', 'currency_rate_converter'); ?></h3>
                    </div>
                    <div data-v-4e76d53a="" class="home-banner__block-body form-group">
                        <div data-v-4e76d53a="" class="dropdown">
                            <div data-v-4e76d53a="" class="form-field">
                                <input data-v-4e76d53a="" type="number" id="get_val" class="form-control text-bold form-control--value f-input" placeholder="" value="0" style="color: black; background: transparent; font-weight: bold; font-size: 16px;" readonly>
                                <span id="currency_symbol_right">-</span>
                            </div>
                            <button data-v-4e76d53a="" id="toggle_right_button" onclick="toggleSidebar_right()" class="btn" data-open="right">
                                <div data-v-4e76d53a="" id="vtov" data-group="crypto">
                                    <span data-v-4e76d53a="" class="name" id="get_currency"><?php _e('Select Currency', 'currency_rate_converter'); ?></span>
                                </div>
                                <svg data-v-4e76d53a="" class="ml-auto" width="11" height="6" viewBox="0 0 11 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path data-v-4e76d53a="" fill-rule="evenodd" clip-rule="evenodd" d="M.782.12a.44.44 0 01.637.034L5.497 4.85 9.575.154a.44.44 0 01.637-.034.48.48 0 01.035.661L5.849 5.846A.441.441 0 015.497 6a.441.441 0 01-.352-.154L.747.781a.48.48 0 01.035-.66z" fill="#484D56"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div data-v-4e76d53a="" class="justify-center text-center text-white">
                        <span data-v-4e76d53a=""><?php _e('Rate:', 'currency_rate_converter'); ?>&nbsp;<span id="crc_rates">0</span></span> | <span data-v-4e76d53a=""><?php _e('Fees:', 'currency_rate_converter'); ?>&nbsp;</span><span id="crc_fees">0</span>
                        <i data-v-4e76d53a="" class="q-icon text-white fas fa-question bg-grey-8" aria-hidden="true" role="presentation" flat="" clickable="" rounded="" style="padding: 3px; border-radius: 10px;"> </i>
                    </div>
                </div>
            </div>
            <div data-v-4e76d53a="" class="col-lg-12 q-my-md justify-center text-center">
                <input type="hidden" id="left-currency-id" value="">
                <input type="hidden" id="right-currency-id" value="">
                <input type="hidden" id="crc_conversion" value="">
                <input type="hidden" id="minimum_amount" value="">
                <input type="hidden" id="crc_fee" value="">
                <input type="hidden" id="currency_redirect_link" value="">
                <button data-v-4e76d53a="" id="next_button" class="q-btn q-btn-item non-selectable no-outline q-btn--standard q-btn--rectangle q-btn--actionable q-focusable q-hoverable btn btn-primary-withstuff text-white btn-block crc_disabled" tabindex="0" type="submit" style="max-width: 200px;" onclick="openPopup()">
                    <span class="q-focus-helper" tabindex="-1"></span>
                    <span class="q-btn__content text-center items-center q-anchor--skip justify-center "><?php _e('Next', 'currency_rate_converter'); ?></span>
                </button>
            </div>

            <div class="popup-overlay" id="popup-overlay"></div>

            <div class="popup" id="popup">
                <div class="popup-content">
                    <span class="close-btn-popup" onclick="closePopup()">&times;</span>
                    <h4><?php _e('Detailed Information', 'currency_rate_converter'); ?></h4>
                    <p><?php _e('Amount Send: ', 'currency_rate_converter'); ?><span id="crc_amount_sent"></span> </p>
                    <p><?php _e('Conversion Rate: ', 'currency_rate_converter'); ?> <span id="crc_converion_rate"></span></p>
                    <p><?php _e('Total Amount: ', 'currency_rate_converter'); ?><span id="crc_total_amount"></span></p>
                    <p><?php _e('Platform Fee: ', 'currency_rate_converter'); ?><span id="crc_platform_fee"></span></p>
                    <p>
                        <b><?php _e('Amount Receive will be: ', 'currency_rate_converter'); ?></b>
                        <span id="crc_amount_recieve"></span><span  onclick="copyValue()">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="copy-value">
                                <path d="M384 336H192c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16l140.1 0L400 115.9V320c0 8.8-7.2 16-16 16zM192 384H384c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1H192c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V416H272v32c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192c0-8.8 7.2-16 16-16H96V128H64z"/>
                            </svg>
                        </span>
                    </p>
                    <div style="text-align: center;">
                        <a href="#" target="_blank" data-v-4e76d53a="" id="crc_link_btn" class="q-btn q-btn-item non-selectable no-outline q-btn--standard q-btn--rectangle q-btn--actionable q-focusable q-hoverable btn btn-primary-withstuff text-white btn-block" style="max-width: 150px; padding: 10px 20px; margin:auto !important; text-decoration: none;"><?php _e('Go to order page', 'currency_rate_converter'); ?></a>
                    </div>
                </div>
            </div>

        </div>
<?php
    }
}
add_shortcode('crc_currency_exchange_shortcode', 'crc_currency_exchange_function');
//Shortcode Render End

?>