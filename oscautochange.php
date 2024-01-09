<?php

/**
 * Insert new lines in "includes/template_top.php" file
 */
$templateTop = 'includes/template_top.php';
$templateTopMatch = 'src= "includes/javascript/lunar.js"';
$templateTopFind = '<head>';
$templateTopInsert = '
<head>
    <?php if ( basename( $PHP_SELF ) == \'checkout_confirmation.php\' ) : ?>
        <script src= "includes/javascript/lunar.js"></script>
    <?php endif ?>
';

modifyFileContents($templateTop, $templateTopMatch, $templateTopFind, $templateTopInsert);

/**
 * Insert new lines at the end in "includes/.htaccess" file
 */
$htaccess = 'includes/.htaccess';
$htaccessMatch = 'FilesMatch "lunar.php"';
$htaccessFind = '';
$htaccessInsert = '
<FilesMatch "lunar.php">
    Allow from all
</FilesMatch>
';

modifyFileContents($htaccess, $htaccessMatch, $htaccessFind, $htaccessInsert, $endOfFile = true);

/**
 * Insert (I) new lines in "admin/modules.php" file
 */
$modules_1 = 'admin/modules.php';
$modules_1_Match = 'payment/lunar/errors.php';
$modules_1_Find = 'require(\'includes/application_top.php\');';
$modules_1_Insert = '
  require_once(\'includes/modules/payment/lunar/errors.php\');
  require(\'includes/application_top.php\');
';

modifyFileContents($modules_1, $modules_1_Match, $modules_1_Find, $modules_1_Insert);

/**
 * Insert (II) new lines in "admin/modules.php" file
 */
$modules_2 = 'admin/modules.php';
$modules_2_Match = 'payment/lunar/validate.php';
$modules_2_Find = 'reset($HTTP_POST_VARS[\'configuration\']);';
$modules_2_Insert = '
        reset($HTTP_POST_VARS[\'configuration\']);
        require_once(\'includes/modules/payment/lunar/validate.php\');
';

modifyFileContents($modules_2, $modules_2_Match, $modules_2_Find, $modules_2_Insert);

/**
 * Insert (III) new lines in "admin/modules.php" file
 */
$modules_3 = 'admin/modules.php';
$modules_3_Match = 'if(sizeof($errors) === 0 || array_search($key, $validation_keys) === FALSE)';
$modules_3_Find = 'tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = \'" . $value . "\' where configuration_key = \'" . $key . "\'");';
$modules_3_Insert = '
            if(sizeof($errors) === 0 || array_search($key, $validation_keys) === FALSE){
                tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = \'" . $value . "\' where configuration_key = \'" . $key . "\'");
            }
';

modifyFileContents($modules_3, $modules_3_Match, $modules_3_Find, $modules_3_Insert);

/**
 * Insert (IV) new lines in "admin/modules.php" file
 */
$modules_4 = 'admin/modules.php';
$modules_4_Match = 'if(sizeof($errors)){';
$modules_4_Find = 'tep_redirect(tep_href_link(FILENAME_MODULES, \'set=\' . $set . \'&module=\' . $HTTP_GET_VARS[\'module\']));';
$modules_4_Insert = '
        if(sizeof($errors)){
            tep_redirect(tep_href_link(FILENAME_MODULES, \'set=\' . $set . \'&module=\' . $HTTP_GET_VARS[\'module\'] . \'&action=edit\'));
        }
        tep_redirect(tep_href_link(FILENAME_MODULES, \'set=\' . $set . \'&module=\' . $HTTP_GET_VARS[\'module\']));
';

modifyFileContents($modules_4, $modules_4_Match, $modules_4_Find, $modules_4_Insert);

/**
 * Insert (V) new lines in "admin/modules.php" file
 */
$modules_5 = 'admin/modules.php';
$modules_5_Match = '(isset($errorHandler))$errorHandler->display()';
$modules_5_Find = 'case \'edit\':';
$modules_5_Insert = '
    case \'edit\':
      if(isset($errorHandler))$errorHandler->display();
';

modifyFileContents($modules_5, $modules_5_Match, $modules_5_Find, $modules_5_Insert);



/** SELF-DELETE this file. */
echo 'Now the script will be deleted';
unlink(__FILE__);



/** ***************************** FUNCTION AREA ****************************************** */

/**
 * Modify file contents with desired text to insert
 * - search if text to insert is present
 * - replace portion of text with another OR insert text to the end of the file
 *
 * @param string $relativeFilePath - the file relative path as 'folder/anotherFolder/file.ext'
 * @param string $matchText - if this text is present the file will be untouched
 * @param string $findText - the text where to begin insertion
 * @param string $insertText - the text to be inserted
 * @param bool $endOfFile - specify if the desired text has to be inserted to the end of the file
 *
 * @return void
 *
 */

function modifyFileContents($relativeFilePath, $matchText, $findText, $insertText, $endOfFile = false) : void
{
    try {
        /** Check file existence. */
        if (file_exists($relativeFilePath)) {
            $file = file_get_contents($relativeFilePath);
        } else {
            echo 'File not exists -> ' . $relativeFilePath . PHP_EOL . '<br>';
        }

        /** Check if the file NOT contains the text for insertion. */
        if (false === strstr($file, $matchText)) {

            /** Check if "$endOfFile" is set to false. */
            if (false === $endOfFile) {
                /** Insert the new code lines. */
                $modifiedFileContents =
                    str_replace(
                        $search = $findText,
                        $replace = $insertText,
                        $subject = $file
                    );

                /** Write modified contents to the file. */
                file_put_contents($relativeFilePath, $modifiedFileContents);

            } else {
                /** Append the new code lines and writes to the file. */
                file_put_contents($relativeFilePath, $insertText, FILE_APPEND);
            }

            echo 'Successfully changed ' . $relativeFilePath  . PHP_EOL . '<br>';

        } else {
            echo 'Changes already applied to ' . $relativeFilePath . PHP_EOL . '<br>';
        }

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}