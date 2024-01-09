<?php
if(strpos($_SERVER['REQUEST_URI'], 'module=lunar')){
    /* Initialize errors object */
    require_once('helpers/Lunar_Errors.php');
    $errorHandler = new LunarErrors();
}
