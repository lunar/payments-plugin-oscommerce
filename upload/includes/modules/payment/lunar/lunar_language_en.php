<?php

define('MODULE_PAYMENT_LUNAR_CARD_ADMIN_TITLE', 'Lunar Card');
define('MODULE_PAYMENT_LUNAR_CARD_ADMIN_DESCRIPTION', 'Receive payments with card via &copy; Lunar');
define('MODULE_PAYMENT_LUNAR_MOBILEPAY_ADMIN_TITLE', 'Lunar MobilePay');
define('MODULE_PAYMENT_LUNAR_MOBILEPAY_ADMIN_DESCRIPTION', 'Receive payments with MobilePay via &copy; Lunar');

// ADMIN SETTINGS

define('LUNAR_ADMIN_ENABLE_TITLE', 'Enable/Disable');
define('LUNAR_ADMIN_ENABLE_DESCRIPTION', '');

define('LUNAR_ADMIN_METHOD_TITLE_VALUE_CARD', 'Card');
define('LUNAR_ADMIN_METHOD_TITLE_VALUE_MOBILEPAY', 'MobilePay');
define('LUNAR_ADMIN_METHOD_TITLE_TITLE', 'Payment method title');
define('LUNAR_ADMIN_METHOD_TITLE_DESCRIPTION', '');
define('LUNAR_ADMIN_METHOD_DESCRIPTION_VALUE_CARD', 'Secure payment with card via © Lunar');
define('LUNAR_ADMIN_METHOD_DESCRIPTION_VALUE_MOBILEPAY', 'Secure payment with MobilePay via © Lunar');

define('LUNAR_ADMIN_METHOD_DESCRIPTION_TITLE', 'Payment method description');
define('LUNAR_ADMIN_METHOD_DESCRIPTION_DESCRIPTION', '');

define('LUNAR_ADMIN_SHOP_TITLE', 'Shop title');
define('LUNAR_ADMIN_SHOP_DESCRIPTION', 'The text shown in the page where the customer is redirected');

define('LUNAR_ADMIN_APP_KEY_TITLE', 'App Key');
define('LUNAR_ADMIN_APP_KEY_DESCRIPTION', 'Get it from your Lunar dashboard');

define('LUNAR_ADMIN_PUBLIC_KEY_TITLE', 'Public Key');
define('LUNAR_ADMIN_PUBLIC_KEY_DESCRIPTION', 'Get it from your Lunar dashboard');

define('LUNAR_ADMIN_CONFIGURATION_ID_TITLE', 'Configuration ID');
define('LUNAR_ADMIN_CONFIGURATION_ID_DESCRIPTION', 'Email onlinepayments@lunar.app to get it');

define('LUNAR_ADMIN_LOGO_URL_TITLE', 'Logo URL');
define('LUNAR_ADMIN_METHOD_LOGO_URL_DESCRIPTION', 'Must be a link begins with "https://" to a JPG,JPEG or PNG file');

define('LUNAR_ADMIN_CAPTURE_MODE_TITLE', 'Capture mode');
define('LUNAR_ADMIN_CAPTURE_MODE_INSTANT', 'Instant');
define('LUNAR_ADMIN_CAPTURE_MODE_DELAYED', 'Delayed');
define('LUNAR_ADMIN_CAPTURE_MODE_DESCRIPTION', 'If you deliver your product instantly (e.g. a digital product), choose Instant mode. If not, use Delayed. In delayed mode you can capture the payment via the Transaction ID panel on the order edit page');

define('LUNAR_ADMIN_AUTHORIZE_STATUS_TITLE', 'On authorize set order status to:');
define('LUNAR_ADMIN_AUTHORIZE_STATUS_DESCRIPTION', 'When an authorization is made the order gets moved into this status');

define('LUNAR_ADMIN_CAPTURE_STATUS_TITLE', 'On capture set order status to:');
define('LUNAR_ADMIN_CAPTURE_STATUS_DESCRIPTION', 'When a capture is made the order gets moved into this status');

define('LUNAR_ADMIN_PAYMENT_ZONE_TITLE', 'Lunar Payment Zone');
define('LUNAR_ADMIN_PAYMENT_ZONE_DESCRIPTION', 'If you select a zone, you will limit the payment method for that zone');

define('LUNAR_ADMIN_SORT_ORDER_TITLE', 'Sort order');
define('LUNAR_ADMIN_SORT_ORDER_DESCRIPTION', 'Sort order for payment method. Lowest is displayed first.');


// GATEWAY ERRORS

define('LUNAR_ERROR', 'Error:');
define('LUNAR_ERROR_NOT_FOUND', 'Transaction not found! Check the transaction key used for the operation.');
define('LUNAR_ERROR_INVALID_REQUEST', 'The request is not valid! Check if there is any validation after this message and adjust if possible, if not, and the problem persists, contact the developer.');
define('LUNAR_ERROR_FORBIDDEN', 'The operation is not allowed! You do not have the rights to perform the operation, make sure you have all the grants required on your Lunar account.');
define('LUNAR_ERROR_UNAUTHORIZED', 'The operation is not properly authorized! Check the credentials set in settings for Lunar.');
define('LUNAR_ERROR_CONFLICT', 'The operation leads to a conflict! The same transaction is being requested for modification at the same time. Try again later.');
define('LUNAR_ERROR_API_CONNECTION', 'Network issues ! Check your connection and try again.');
define('LUNAR_ERROR_EXCEPTION', 'There has been a server issue! If this problem persists contact the developer.');


// ADMIN COMMENTS

define('LUNAR_COMMENT_AUTHORIZE', 'Funds Authorized. Transaction ID: ');
define('LUNAR_COMMENT_CAPTURE', 'Funds Captured. Transaction ID: ');
define('LUNAR_COMMENT_AMOUNT', 'Amount: ');
define('LUNAR_COMMENT_ORDER', 'Order: ');
define('LUNAR_COMMENT_CAPTURE_SUCCESS', 'Transaction captured successfully. Order: ');
define('LUNAR_COMMENT_CAPTURE_FAILURE', 'Error when capturing -- transaction_id: ');
define('LUNAR_COMMENT_TRANSACTION_NOT_FOUND', 'Transaction id is not found. Order:');
define('LUNAR_COMMENT_TRANSACTION_EMPTY', 'Either transaction id is null or empty. Order:');
define('LUNAR_COMMENT_TRANSACTION_FETCH_ISSUE', 'Transaction details couldn\'t be retrieved. Transaction id:');


// ADMIN WARNINGS
define('LUNAR_WARNING_NOT_CONFIGURED', 'Method Not Configured');
define('LUNAR_WARNING_NOT_CONFIGURED_FRONTEND', 'Lunar module is not configured yet.');


// ORDER SYSTEM GATEWAY ERROR
define('LUNAR_ORDER_ERROR_AMOUNT_CURRENCY_MISMATCH', 'The transaction amount or currency mismatch. Please try again or contact system administrator.');
define('LUNAR_ORDER_ERROR_PAYMENT_INTENT_NOT_FOUND', 'Transaction ID not found. Please try again or contact system administrator.');

// PAYMENT STATUSES

define('LUNAR_STATUS_AUTHORIZED', 'Authorized');
define('LUNAR_STATUS_CAPTURED', 'Captured');
