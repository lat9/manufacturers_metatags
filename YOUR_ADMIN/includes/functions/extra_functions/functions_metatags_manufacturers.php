<?php
/**
 * metatags-editing functions
 *
 * @package admin
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: functions_metatags_manufacturers.php 2844 2006-01-13 06:46:29Z drbyte $
 * @no-docs
 */


/**
 * Manufacturers specific metatags
 */
  function zen_get_manufacturer_metatags_manu_title($manufacturer_id, $language_id) {
    global $db;
    $category = $db->Execute("select metatags_title
                              from ".TABLE_MANUFACTURERS_META.
                              " where manufacturers_id = '" . (int)$manufacturer_id . "'
                              and language_id = '" . (int)$language_id . "'");

    return $category->fields['metatags_title'];
  }

  function zen_get_manufacturer_metatags_manu_description($manufacturer_id, $language_id) {
    global $db;
    $category = $db->Execute("select metatags_description
                              from ".TABLE_MANUFACTURERS_META.
                              " where manufacturers_id = '" . (int)$manufacturer_id . "'
                              and language_id = '" . (int)$language_id . "'");

    return $category->fields['metatags_description'];
  }

  function zen_get_manufacturer_metatags_manu_keywords($manufacturer_id, $language_id) {
    global $db;
    $category = $db->Execute("select metatags_keywords
                              from ".TABLE_MANUFACTURERS_META.
                              " where manufacturers_id = '" . (int)$manufacturer_id . "'
                              and language_id = '" . (int)$language_id . "'");

    return $category->fields['metatags_keywords'];
  }

/**
 *
 */
 
?>