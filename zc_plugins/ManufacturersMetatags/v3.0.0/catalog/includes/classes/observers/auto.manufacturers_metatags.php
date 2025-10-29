<?php
// -----
// Part of the Manufacturers Metatags plugin by lat9 (lat9@vinosdefrutastropicales.com).
// Copyright (C) 2025, Vinos de Frutas Tropicales
//
// Last updated: v3.0.0
//
use Zencart\Plugins\Catalog\ManufacturersMetatags\ManufacturersMetatags;

class zcObserverManufacturersMetatags extends \base
{
    protected array $metaTags;
    protected string $manufacturersName;

    public function __construct()
    {
        $this->attach($this, [
            'NOTIFY_MODULE_START_META_TAGS',
        ]);
    }

    public function notify_module_start_meta_tags(&$class, string $e, string $current_page_base, string &$meta_tag_page_name, bool &$meta_tags_over_ride): void
    {
        if ($current_page_base !== FILENAME_DEFAULT || !isset($_GET['manufacturers_id'])) {
            return;
        }

        $manufacturers_id = (int)$_GET['manufacturers_id'];
        $mmt = new ManufacturersMetatags($manufacturers_id);
        $this->metaTags = $mmt->getMetaTagsForLanguage((int)$_SESSION['languages_id']);
        $tag_check = $this->metaTags['metatags_title'] . $this->metaTags['metatags_keywords'] . $this->metaTags['metatags_description'];
        if ($tag_check === '') {
            return;
        }
        
        global $db;
        $name = $db->Execute(
            "SELECT manufacturers_name
               FROM " . TABLE_MANUFACTURERS . "
              WHERE manufacturers_id = $manufacturers_id
              LIMIT 1"
        );
        if ($name->EOF) {
            return;
        }
        
        $this->manufacturersName = $name->fields['manufacturers_name'];

        $meta_tag_page_name = 'index_manufacturers';
        $this->attach($this, [
            'NOTIFY_MODULE_META_TAGS_UNSPECIFIEDPAGE',
        ]);
    }

    public function notify_module_meta_tags_unspecifiedpage(&$class, string $e, $unused, string $metatag_page_name, bool &$meta_tags_over_ride, string &$metatags_title, string &$metatags_description, string &$metatags_keywords): void
    {
        $metatags_title = ($this->metaTags['metatags_title'] === '') ? $this->manufacturers_name : $this->metaTags['metatags_title'];
        zen_define_default('META_TAG_TITLE', str_replace(["'", '"'], '', strip_tags($metatags_title . PRIMARY_SECTION . TITLE . TAGLINE)));

        $metatags_keywords = ($this->metaTags['metatags_keywords'] === '') ? $this->manufacturers_name . METATAGS_DIVIDER . KEYWORDS : $this->metaTags['metatags_keywords'];
        zen_define_default('META_TAG_KEYWORDS', str_replace(["'", '"'], '', strip_tags($metatags_keywords)));

        $metatags_description = ($this->metaTags['metatags_description'] === '') ? $this->manufacturers_name . SECONDARY_SECTION . KEYWORDS : $this->metaTags['metatags_description'];
        zen_define_default('META_TAG_DESCRIPTION', str_replace(["'", '"'], '', strip_tags(TITLE . PRIMARY_SECTION . $metatags_description)));
    }
}
