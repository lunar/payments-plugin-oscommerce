<?php

$rootPath = dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
chdir('../../');

require($rootPath.'/includes/application_top.php');

include(DIR_WS_LANGUAGES . $language . '/checkout_process.php');
include(DIR_WS_LANGUAGES . $language . '/checkout.php');

if (!class_exists('osC_onePageCheckout') ) {
    require( 'includes/classes/onepage_checkout.php' );
}

$onePageCheckout = new osC_onePageCheckout();
// generate user data from database (table "orders"):
$onePageCheckout->generate_email_by_order_id($order_id);

// send message only once. if we send email, change customer_notified to 1.
$check_status_query = tep_db_query('SELECT `orders_id` FROM ' . TABLE_ORDERS_STATUS_HISTORY . ' WHERE `customer_notified` = 1 AND `orders_id` = "' . $order_id . '"');
if (tep_db_num_rows($check_status_query) == 0) {

    // for create emails:
    $onePageCheckout->createEmails($order_id);

    tep_db_perform('orders_status_history', [
        'orders_id' => $order_id,
        'orders_status_id' => $order_status,
        'date_added' => 'now()',
        'customer_notified' => '1'
    ]);

}

// if success - change order status to success:
tep_db_perform('orders', ['orders_status' => MODULE_PAYMENT_LUNAR_ORDER_STATUS_ID], 'update', "orders_id='" . $order_id . "'");
tep_db_perform('orders_status_history', [
    'orders_id' => $order_id,
    'orders_status_id' => MODULE_PAYMENT_LUNAR_ORDER_STATUS_ID,
    'date_added' => 'now()',
    'customer_notified' => '0',
    'comments' => 'Lunar - success!'
]);

// send email to customer:
$payment_method = 'Lunar';
if($content_email_array = get_email_contents('success_payment')){

    $store_categories = '';
    $store_categories_query=tep_db_query("select categories_id, categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where language_id = '" . (int)$languages_id . "' limit 5");
    while($sc_array=tep_db_fetch_array($store_categories_query)) {
        $store_categories .= '<a style="text-decoration:underline;color:inherit" href="'.$_SERVER['HTTP_HOST'].'/index.php?cPath='.$sc_array['categories_id'].'"><span>'.$sc_array['categories_name'].'</span></a><span style="padding:0 5px">&bull;</span>';
    }

    // array to replace variables from html template:
    $array_from_to = array (
        '{STORE_NAME}' =>         STORE_NAME,
        '{CUSTOMER_NAME}' =>      $customer_name,
        '{ORDER_ID}' =>           $order_id,
        '{PAYMENT_METHOD}' =>     $payment_method,
        '{STORE_LOGO}' =>         HTTP_SERVER . '/' . LOGO_IMAGE,
        '{STORE_URL}' =>          HTTP_SERVER,
        '{STORE_OWNER_EMAIL}' =>  STORE_OWNER_EMAIL_ADDRESS,
        '{STORE_ADDRESS}' =>      strip_tags(renderArticle('contacts_footer')),
        '{STORE_PHONE}' =>        strip_tags(renderArticle('phones')),
        '{STORE_CATEGORIES}' =>   $store_categories);

    $email_text = strtr($content_email_array['content_html'], $array_from_to);
}
tep_mail($customer_name, $order->customer['email_address'], sprintf($content_email_array['subject'],$order_id,strftime(DATE_FORMAT_LONG)), $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);


$_SESSION['callback'] = true;
tep_redirect(HTTP_SERVER.'/'.FILENAME_CHECKOUT_SUCCESS);