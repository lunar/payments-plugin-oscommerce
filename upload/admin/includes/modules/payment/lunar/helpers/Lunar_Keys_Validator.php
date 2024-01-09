<?php
/**
 * Helper class that validates module keys via Lunar API
 */
class LunarValidator
{
    public $validationPublicKeys = [];
    /**
     * Validate the App key.
     *
     * @param string $value - the value of the input.
     *
     * @return string - the error message
     */
    public function validateAppKeyField($value)
    {
        /** Check if the key value is empty **/
        if (! $value) {
            return ERROR_APP_KEY;
        }
        /** Load the client from API**/
        $apiClient = new \Lunar\Lunar($value);
        try {
            /** Load the identity from API**/
            $identity = $apiClient->apps()->fetch();
        } catch (\Lunar\Exception\ApiException $exception) {
            $error = ERROR_APP_KEY_INVALID;
            self::logMessage($error);
            return $error;
        }

        try {
            /** Load the merchants public keys list corresponding for current identity **/
            $merchants = $apiClient->merchants()->find($identity['id']);
            if ($merchants) {
                foreach ($merchants as $merchant) {
                    // $this->validationPublicKeys[] = $merchant['key']; //@TODO check this
                }
            }
        } catch (\Lunar\Exception\ApiException $exception) {
            self::logMessage(ERROR_APP_KEY_INVALID);
        }
        /** Check if public keys array for the current mode is populated **/
        if (empty($this->validationPublicKeys)) {
            /** Generate the error based on the current mode **/
            $error = ERROR_APP_KEY_INVALID_MODE;
            self::logMessage($error);
            return $error;
        }
    }

    /**
     * Validate the Public key.
     *
     * @param string $value - the value of the input.
     * @param string $mode - the transaction mode 'test' | 'live'.
     *
     * @return string - the error message
     */
    public function validatePublicKeyField($value)
    {
        /** Check if the key value is not empty **/
        if (! $value) {
            return ERROR_PUBLIC_KEY;
        }
        /** Check if the local stored public keys array is empty OR the key is not in public keys list **/
        if (empty($this->validationPublicKeys) || ! in_array($value, $this->validationPublicKeys)) {
            $error = ERROR_PUBLIC_KEY_INVALID;
            self::logMessage($error);
            return $error;
        }
    }

    /**
     * log message to default logger
     *
     * @param string $message
     *
     */
    public static function logMessage($message)
    {
        error_log('[Lunar] ' . $message);
    }
}
