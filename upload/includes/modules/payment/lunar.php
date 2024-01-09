<?php
class lunar {

    var $code, $enabled, $app, $response, $key, $appKey, $sort_order, 
        $redirect_url, $success_url, $title, $formTitle, $paymentType, $catalog_dir, $admin_dir;

    function __construct() {
        global $order;

        $this->code = 'lunar';
        $this->redirect_url = HTTP_SERVER . '/includes/callbacks/lunar_callback.php';
        $this->sort_order = MODULE_PAYMENT_LUNAR_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_LUNAR_STATUS == 'True') ? true : false);
        $this->key = MODULE_PAYMENT_LUNAR_PUBLIC_KEY;
        $this->appKey = MODULE_PAYMENT_LUNAR_APP_KEY;
        $this->title = MODULE_PAYMENT_LUNAR_TEXT_TITLE;
        $this->formTitle = $_SESSION['LUNAR_TITLE'] = MODULE_PAYMENT_LUNAR_SHOP_TITLE;
        $this->paymentType = MODULE_PAYMENT_LUNAR_PAYMENT_TYPE;

        $this->catalog_dir = HTTPS_CATALOG_SERVER.DIR_WS_HTTPS_CATALOG;
        $this->admin_dir = HTTPS_SERVER.DIR_WS_HTTPS_ADMIN;

        if (strpos($_SERVER['REQUEST_URI'], 'action=edit')) {
            // echo '<script type="text/javascript" src="'.$this->admin_dir.'includes/modules/payment/lunar/lunar.js"></script>';
            echo '<link rel="stylesheet" type="text/css" href="'.$this->admin_dir.'includes/modules/payment/lunar/lunar.css"/>';
        }
    }

    function selection() {
        global $cart_payment_id;
        if (tep_session_is_registered('cart_payment_id')) {
            $order_id = substr($cart_payment_id, strpos($cart_payment_id, '-') + 1);

            $check_query = tep_db_query('SELECT `orders_id` FROM ' . TABLE_ORDERS_STATUS_HISTORY . ' WHERE `orders_id` = "' . (int)$order_id . '" LIMIT 1');

            if (tep_db_num_rows($check_query)<1) {
                tep_db_query('DELETE FROM ' . TABLE_ORDERS . ' WHERE `orders_id` = "' . (int)$order_id . '"');
                tep_db_query('DELETE FROM ' . TABLE_ORDERS_TOTAL . ' WHERE orders_id = "' . (int)$order_id . '"');
                tep_db_query('DELETE FROM ' . TABLE_ORDERS_STATUS_HISTORY . ' WHERE orders_id = "' . (int)$order_id . '"');
                tep_db_query('DELETE FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE `orders_id` = "' . (int)$order_id . '"');
                tep_db_query('DELETE FROM ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' WHERE orders_id = "' . (int)$order_id . '"');
                tep_db_query('DELETE FROM ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' WHERE orders_id = "' . (int)$order_id . '"');

                tep_session_unregister('cart_payment_id');
            }
        }

        if (tep_not_null($this->icon)) $icon = tep_image($this->icon, $this->title);

        $title = $this->title;
        $title .= '<img style="width: 45px;margin-left: 5px;" src="'.DIR_WS_IMAGES.'modules/payment/lunar/maestro.svg">';
        $title .= '<img style="width: 45px;margin-left: 5px;" src="'.DIR_WS_IMAGES.'modules/payment/lunar/mastercard.svg">';
        $title .= '<img style="width: 45px;margin-left: 5px;" src="'.DIR_WS_IMAGES.'modules/payment/lunar/visa.svg">';
        $title .= '<img style="width: 45px;margin-left: 5px;" src="'.DIR_WS_IMAGES.'modules/payment/lunar/visaelectron.svg">';

        return [
            'id' => $this->code,
            'icon' => $icon,
            'sort' => $this->sort_order,
            'module' => $title
        ];

    }

    function pre_confirmation_check() {
        global $cartID, $cart;

        if (empty($cart->cartID)) $cartID = $cart->cartID = $cart->generate_cart_id();
        if (!tep_session_is_registered('cartID')) tep_session_register('cartID');

    }

    function confirmation() {
        global $cartID, $cart_payment_id, $order, $order_total_modules, $onePageCheckout, $order_totals;

        if (tep_session_is_registered('cartID')) {
            $insert_order = false;

            if (tep_session_is_registered('cart_payment_id')) {
                $order_id = substr($cart_payment_id, strpos($cart_payment_id, '-') + 1);

                $curr_check = tep_db_query("select currency from " . TABLE_ORDERS . " where orders_id = '" . (int)$order_id . "'");
                $curr = tep_db_fetch_array($curr_check);

                if (($curr['currency'] != $order->info['currency']) || ($cartID != substr($cart_payment_id, 0, strlen($cartID)))) {
                    $check_query = tep_db_query('select orders_id from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int)$order_id . '" limit 1');

                    if (tep_db_num_rows($check_query)<1) {
                        tep_db_query('DELETE FROM ' . TABLE_ORDERS . ' WHERE orders_id = "' . (int)$order_id . '"');
                        tep_db_query('DELETE FROM ' . TABLE_ORDERS_TOTAL . ' WHERE orders_id = "' . (int)$order_id . '"');
                        tep_db_query('DELETE FROM ' . TABLE_ORDERS_STATUS_HISTORY . ' WHERE orders_id = "' . (int)$order_id . '"');
                        tep_db_query('DELETE FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE `orders_id` = "' . (int)$order_id . '"');
                        tep_db_query('DELETE FROM ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' WHERE orders_id = "' . (int)$order_id . '"');
                        tep_db_query('DELETE FROM ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' WHERE orders_id = "' . (int)$order_id . '"');
                    }

                    // $insert_order = true;
                }
            } else {
                // $insert_order = true;
            }
            if (class_exists($onePageCheckout)) {
                $onePageCheckout->loadSessionVars();
            }
            if (!isset($order_totals)) {
                $order_totals = $order_total_modules->process();
            }
            // if ($insert_order) {
                // if (class_exists($onePageCheckout)) {
                //   $insert_id = $onePageCheckout->createOrder(MODULE_PAYMENT_LUNAR_ORDER_STATUS_ID);
                // } else {
                    // $this->createOrder();
                // }
            // }

        }

        return [
            'title' => MODULE_PAYMENT_LUNAR_TEXT_DESCRIPTION,
        ];
    }

    function createOrder() {
        
        global $insert_id, $cartID, $order, $customer_id, $order_total_modules, $order_totals;

        $order_totals = [];
        if (is_array($order_total_modules->modules)) {
            reset($order_total_modules->modules);
            while (list (, $value) = each($order_total_modules->modules)) {
                $class = substr($value, 0, strrpos($value, '.'));
                if ($GLOBALS[$class]->enabled) {
                    for ($i = 0, $n = sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
                        if (tep_not_null($GLOBALS[$class]->output[$i]['title']) && tep_not_null($GLOBALS[$class]->output[$i]['text'])) {
                            $order_totals[] = [
                                'code' => $GLOBALS[$class]->code,
                                'title' => $GLOBALS[$class]->output[$i]['title'],
                                'text' => $GLOBALS[$class]->output[$i]['text'],
                                'value' => $GLOBALS[$class]->output[$i]['value'],
                                'sort_order' => $GLOBALS[$class]->sort_order
                            ];
                        }
                    }
                }
            }
        }

        $sql_data_array = [
            'customers_id' => $customer_id,
            'customers_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
            'customers_company' => $order->customer['company'],
            'customers_street_address' => $order->customer['street_address'],
            'customers_suburb' => $order->customer['suburb'],
            'customers_city' => $order->customer['city'],
            'customers_postcode' => $order->customer['postcode'],
            'customers_state' => $order->customer['state'],
            'customers_country' => $order->customer['country']['title'],
            'customers_telephone' => $order->customer['telephone'],
            'customers_email_address' => $order->customer['email_address'],
            'customers_address_format_id' => $order->customer['format_id'],
            'delivery_name' => $order->delivery['firstname'] . ' ' . $order->delivery['lastname'],
            'delivery_company' => $order->delivery['company'],
            'delivery_street_address' => $order->delivery['street_address'],
            'delivery_suburb' => $order->delivery['suburb'],
            'delivery_city' => $order->delivery['city'],
            'delivery_postcode' => $order->delivery['postcode'],
            'delivery_state' => $order->delivery['state'],
            'delivery_country' => $order->delivery['country']['title'],
            'delivery_address_format_id' => $order->delivery['format_id'],
            'billing_name' => $order->billing['firstname'] . ' ' . $order->billing['lastname'],
            'billing_company' => $order->billing['company'],
            'billing_street_address' => $order->billing['street_address'],
            'billing_suburb' => $order->billing['suburb'],
            'billing_city' => $order->billing['city'],
            'billing_postcode' => $order->billing['postcode'],
            'billing_state' => $order->billing['state'],
            'billing_country' => $order->billing['country']['title'],
            'billing_address_format_id' => $order->billing['format_id'],
            'payment_method' => $order->info['payment_method'],
            'cc_type' => $order->info['cc_type'],
            'cc_owner' => $order->info['cc_owner'],
            'cc_number' => $order->info['cc_number'],
            'cc_expires' => $order->info['cc_expires'],
            'date_purchased' => 'now()',
            'orders_status' => $order->info['order_status'],
            'currency' => $order->info['currency'],
            'currency_value' => $order->info['currency_value']
        ];

        tep_db_perform(TABLE_ORDERS, $sql_data_array);

        $insert_id = tep_db_insert_id();

        for ($i = 0, $n = sizeof($order_totals); $i<$n; $i++) {
            $sql_data_array = [
                'orders_id' => $insert_id,
                'title' => $order_totals[$i]['title'],
                'text' => $order_totals[$i]['text'],
                'value' => $order_totals[$i]['value'],
                'class' => $order_totals[$i]['code'],
                'sort_order' => $order_totals[$i]['sort_order']
            ];

            tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
        }

        for ($i = 0, $n = sizeof($order->products); $i<$n; $i++) {
            $sql_data_array = [
                'orders_id' => $insert_id,
                'products_id' => tep_get_prid($order->products[$i]['id']),
                'products_model' => $order->products[$i]['model'],
                'products_name' => $order->products[$i]['name'],
                'products_price' => $order->products[$i]['price'],
                'final_price' => $order->products[$i]['final_price'],
                'products_tax' => $order->products[$i]['tax'],
                'products_quantity' => $order->products[$i]['qty']
            ];

            tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);

            $order_products_id = tep_db_insert_id();

            if (isset ($order->products[$i]['attributes'])) {
                for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
                    if (DOWNLOAD_ENABLED == 'true') {
                        $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
                                       from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                       left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                       on pa.products_attributes_id=pad.products_attributes_id
                                       where pa.products_id = '" . $order->products[$i]['id'] . "'
                                       and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
                                       and pa.options_id = popt.products_options_id
                                       and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
                                       and pa.options_values_id = poval.products_options_values_id
                                       and popt.language_id = '" . $languages_id . "'
                                       and poval.language_id = '" . $languages_id . "'";
                        $attributes = tep_db_query($attributes_query);
                    } else {
                        $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $order->products[$i]['id'] . "' and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $languages_id . "' and poval.language_id = '" . $languages_id . "'");
                    }
                    $attributes_values = tep_db_fetch_array($attributes);

                    $sql_data_array = [
                        'orders_id' => $insert_id,
                        'orders_products_id' => $order_products_id,
                        'products_options' => $attributes_values['products_options_name'],
                        'products_options_values' => $attributes_values['products_options_values_name'],
                        'options_values_price' => $attributes_values['options_values_price'],
                        'price_prefix' => $attributes_values['price_prefix']
                    ];

                    tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

                    if ((DOWNLOAD_ENABLED == 'true') && isset ($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
                        $sql_data_array = [
                            'orders_id' => $insert_id,
                            'orders_products_id' => $order_products_id,
                            'orders_products_filename' => $attributes_values['products_attributes_filename'],
                            'download_maxdays' => $attributes_values['products_attributes_maxdays'],
                            'download_count' => $attributes_values['products_attributes_maxcount']
                        ];

                        tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
                    }
                }
            }
        }

        $cart_inpay_Standard_ID = $cartID . '-' . $insert_id;
        tep_session_register('cart_inpay_Standard_ID');


    }

    function process_button() {
        global $order;
        $order->info['order_status'] = MODULE_PAYMENT_LUNAR_ORDER_STATUS_ID;
        return '<span id="lunarCheckout" type="submit" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary ui-priority-primary" role="button" aria-disabled="false"><span class="ui-button-icon-primary ui-icon ui-icon-check"></span><span class="ui-button-text">'.IMAGE_BUTTON_CONFIRM_ORDER.'</span></span>'.'<style>form[name=checkout_confirmation] [id*=tdb]{display: none;}</style>';
    }

    function javascript_validation() {
        return false;
    }

    function before_process() {

        global $order_id, $insert_id, $cart_payment_id;

        if ($cart_payment_id != '') {
            $order_id = substr($cart_payment_id, strpos($cart_payment_id, '-') + 1);
        } else {
            $order_id = $insert_id;
        }
    }

    function after_process() {
        global $insert_id, $order, $order_totals;

        if ($this->paymentType === 'Instant') {
            if (isset($_SESSION['lunarId'])) {
                $transactionId = $_SESSION['lunarId'];
                $descriptor = "Order #$insert_id";

                require_once($this->admin_dir . 'includes/modules/payment/lunar/vendor/autoload.php');

                $apiClient = new \Lunar\Lunar($this->appKey);
                $amount = end($order_totals);
                $apps = $apiClient->payments();
                $apps->capture($transactionId, [
                    'amount' => (float)number_format($amount['value'] * $order->info['currency_value'], 2, '.', '') * 100,
                    'currency' => $order->info['currency'],
                    'descriptor' => $descriptor
                ]);
                tep_db_query("INSERT INTO `orders_status_history` SET `comments`='LUNAR CAPTURE',`date_added`=now(), `orders_status_id`='7',`orders_id`='{$insert_id}'");
                tep_db_query("UPDATE `orders` SET `orders_status`=".MODULE_PAYMENT_LUNAR_ORDER_STATUS_ID." WHERE `orders_id`='{$insert_id}'");
            }
        }
        return false;
    }

    function output_error() {
        return false;
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = tep_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_LUNAR_STATUS'");
            $this->_check = tep_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function install() {
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `set_function`, `date_added`) VALUES ('Allow Lunar module', 'MODULE_PAYMENT_LUNAR_STATUS', 'False', '', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `set_function`, `date_added`) VALUES ('Lunar payment type', 'MODULE_PAYMENT_LUNAR_PAYMENT_TYPE', 'Delayed', '', '6', '1', 'tep_cfg_select_option(array(\'Delayed\', \'Instant\'), ', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('App key', 'MODULE_PAYMENT_LUNAR_APP_KEY', '', '', '6', '2', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Public key', 'MODULE_PAYMENT_LUNAR_PUBLIC_KEY', '', '', '6', '2', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Logo URL', 'MODULE_PAYMENT_LUNAR_LOGO_URL', '', '', '6', '2', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Shop title', 'MODULE_PAYMENT_LUNAR_SHOP_TITLE', '', '', '6', '2', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (`configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `date_added`) VALUES ('Sort order', 'MODULE_PAYMENT_LUNAR_SORT_ORDER', '0', '', '6', '3', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('Success status', 'MODULE_PAYMENT_LUNAR_ORDER_STATUS_ID', '0', '', '6', '6', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
        tep_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
        return [
            'MODULE_PAYMENT_LUNAR_STATUS',
            'MODULE_PAYMENT_LUNAR_APP_KEY',
            'MODULE_PAYMENT_LUNAR_PUBLIC_KEY',
            'MODULE_PAYMENT_LUNAR_LOGO_URL',
            'MODULE_PAYMENT_LUNAR_SHOP_TITLE',
            'MODULE_PAYMENT_LUNAR_PAYMENT_TYPE',
            'MODULE_PAYMENT_LUNAR_ORDER_STATUS_ID',
            'MODULE_PAYMENT_LUNAR_SORT_ORDER'
        ];
    }
}

?>
