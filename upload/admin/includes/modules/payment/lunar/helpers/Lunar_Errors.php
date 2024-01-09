<?php
/* Security class check */
if (! class_exists('LunarErrors')) :
    /**
     * Helper class that handles errors via cookies
     */
    class LunarErrors
    {
        public $errors = '';
        private $cookieName = 'validation_errors';

        public function __construct()
        {
            /* Load current cookie value */
            $this->loadCookieErrors();
        }
        /**
         * Dispay errors as HTML list
         */
        public function display()
        {
            if ($this->errors && sizeof($this->errors)) {
                echo '<div class="validation_error">';
                echo '<ul>';
                echo '<li>' . implode('</li><li>', $this->errors) . '</li>';
                echo '</ul></div>';
            }
        }
        /**
         * Set cookie with list of given values
         *
         * @param array $list - the list o errors to be stored
         */
        public function setCookieErrors($list)
        {
            setcookie($this->cookieName, json_encode($list), time()+3600);
        }

        /**
         * Read and store cookie value
         */
        public function loadCookieErrors()
        {
            if(isset($_COOKIE[$this->cookieName])){
              $this->errors = json_decode($_COOKIE[$this->cookieName], true);
              setcookie($this->cookieName, '', time()-3600);
            }
        }
    }
endif; /* End if class_exists. */
