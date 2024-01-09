# osCommerce plugin for Lunar

This plugin is *not* developed or maintained by Lunar but kindly made
available by a user.

Released under the GPL V3 license: https://opensource.org/licenses/GPL-3.0

## Supported osCommerce versions [![Last succesfull test](https://log.derikon.ro/api/v1/log/read?tag=oscommerce&view=svg&label=osCommerce&key=ecommerce&background=5C9CCC)](https://log.derikon.ro/api/v1/log/read?tag=oscommerce&view=html)

## Installation

 Once you have installed osCommerce, follow these simple steps:
  1. Signup at [lunar.io](https://lunar.io) (it’s free)
  2. Create a live account
  3. Create an app key for your osCommerce website
  4. Upload the files in the `upload` folder to root of your osCommerce store.

      NOTE:

      To skip steps 5 - 7, upload `oscautochange.php` file to your osCommerce store root.\
      Then access `YOUR_WEB_URL/oscautochange.php` in browser address bar.\
      Will show log/success messages based on script status and the file `oscautochange.php` will be deleted.\
      Then go to step 8.

  5. In: `includes/template_top.php` add:
      ```
      <?php if ( basename( $PHP_SELF ) == 'checkout_confirmation.php' ) : ?>
          <script src= "includes/javascript/lunar.js"></script>
      <?php endif ?>
      ```
     Anywhere between the `head` tags.
  6. In: `includes/.htaccess` add:
      ```
      <FilesMatch "lunar.php">
         Allow from all
      </FilesMatch>
      ```
      After the last line.
  7. In: `admin/modules.php`
      7.1.Add:
      ```
      require_once('includes/modules/payment/lunar/errors.php');
      ```
      Before require('includes/application_top.php'); line;

      7.2.Add:
      ```
      require_once('includes/modules/payment/lunar/validate.php');
      ```
      After reset($HTTP_POST_VARS['configuration']); line;

      7.3.Replace tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'"); line with:
      ```
      if(sizeof($errors) === 0 || array_search($key, $validation_keys) === FALSE){
        tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "'");
      }
      ```

      7.4.Add:
      ```
      if(sizeof($errors)){
        tep_redirect(tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $HTTP_GET_VARS['module'] . '&action=edit'));
      }
      ```
      Before tep_redirect(tep_href_link(FILENAME_MODULES, 'set=' . $set . '&module=' . $HTTP_GET_VARS['module'])); line;

      7.5.Add:
      ```
      if(isset($errorHandler))$errorHandler->display();
      ```
      After case 'edit': line.
  8. Install the Lunar module from modules -> payment in the admin (click on `Install Module` botton in the right corner)
  9. Insert the app key and your public key in the settings and enable the plugin

## Updating settings

Under the Lunar settings, you can:
 * Update the title that shows up in the payment popup
 * Add test/live keys
 * Set payment mode (test/live)
 * Change the capture type (Instant/Delayed)

 ## How to

 1. Capture
 * In Instant mode, the orders are captured automatically
 * In delayed mode you can capture funds only from the lunar dashboard.
 2. Refund
   * To refund an order you can use the lunar dashboard.
 3. Void
   * To void an order you can use the lunar dashboard.
