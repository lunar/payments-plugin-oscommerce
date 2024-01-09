<?php
$validation_keys = array();
$errors = array();
if(strpos($_SERVER['REQUEST_URI'], 'module=lunar')){
    require_once('vendor/autoload.php');
    require_once('helpers/Lunar_Keys_Validator.php');
    require_once($module_language_directory . $language . '/modules/payment/lunar.php');

    /* Module keys that needs to be validated */
    $validation_keys = [
        'APP_KEY'    => 'MODULE_PAYMENT_LUNAR_APP_KEY',
        'PUBLIC_KEY' => 'MODULE_PAYMENT_LUNAR_PUBLIC_KEY',
    ];

    $errors = validate($HTTP_POST_VARS['configuration']);
    /* In case of errors, write them into cookies */
    if (isset($errorHandler)) {
        $errorHandler->setCookieErrors($errors);
    }
}

/* Validate module keys */
function validate($vars)
{
    global $validation_keys;

    /* Initialize validator object */
    $validator = new LunarValidator();
    $errors = array();

    $error = $validator->validateAppKeyField($validation_keys['APP_KEY']);
    if (strlen($error)) {
        $errors[] = $error;
    }

    $error = $validator->validatePublicKeyField($validation_keys['PUBLIC_KEY']);
    if (strlen($error)) {
        $errors[] = $error;
    }

    return $errors;
}
