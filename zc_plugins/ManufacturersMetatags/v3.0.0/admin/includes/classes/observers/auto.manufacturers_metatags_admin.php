<?php
// -----
// Part of the Manufacturers Metatags plugin by lat9 (lat9@vinosdefrutastropicales.com).
// Copyright (C) 2025, Vinos de Frutas Tropicales
//
// Last updated: v3.0.0
//
use Zencart\Plugins\Catalog\ManufacturersMetatags\ManufacturersMetatags;

class zcObserverManufacturersMetatagsAdmin extends \base
{
    public function __construct()
    {
        $this->attach($this, [
            'NOTIFY_ADMIN_MANUFACTURERS_INSERT_UPDATE_COMPLETE',    //- Note: Added in zc220
            'NOTIFY_ADMIN_MANUFACTURERS_NEW',
            'NOTIFY_ADMIN_MANUFACTURERS_EDIT',
        ]);
    }

    public function notify_admin_manufacturers_insert_update_complete(&$class, string $e, array $action_id): void
    {
        $manufacturers_id = (int)$action_id['manufacturers_id'];

        global $languages;
        foreach ($languages as $next_lang) {
            $lang_id = (int)$next_lang['id'];

            $sql_data_array = [
                'metatags_title' => zen_db_prepare_input($_POST['metatags_title'][$lang_id]),
                'metatags_keywords' => zen_db_prepare_input($_POST['metatags_keywords'][$lang_id]),
                'metatags_description' => zen_db_prepare_input($_POST['metatags_description'][$lang_id]),
            ];
            zen_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = $manufacturers_id AND languages_id = $lang_id");
        }
    }

    public function notify_admin_manufacturers_new(&$class, string $e, $unused, false|array &$additional_content): void
    {
        if ($additional_content === false) {
            $additional_content = [];
        }
        $additional_content = array_merge($additional_content, $this->getFormFields(0));
    }

    public function notify_admin_manufacturers_edit(&$class, string $e, \objectInfo $mInfo, false|array &$additional_content): void
    {
        if ($additional_content === false) {
            $additional_content = [];
        }
        $additional_content = array_merge($additional_content, $this->getFormFields((int)$mInfo->manufacturers_id));
    }

    protected function getFormFields(int $manufacturers_id): array
    {
        zen_define_default('TEXT_EDIT_MANUFACTURER_META_TAGS_TITLE', 'Metatags Title:');
        zen_define_default('TEXT_EDIT_MANUFACTURER_META_TAGS_KEYWORDS', 'Metatags Keywords:');
        zen_define_default('TEXT_EDIT_MANUFACTURER_META_TAGS_DESCRIPTION', 'Metatags Description:');

        $mmt = new ManufacturersMetatags($manufacturers_id);
        $metatags = $mmt->getAllMetaTags();

        $titles = '';
        $title_field_length = zen_set_field_length(TABLE_MANUFACTURERS_INFO, 'metatags_title');

        $keywords = '';
        $description = '';
        global $languages;
        foreach ($languages as $next_lang) {
            $lang_id = $next_lang['id'];
            $language_addon =
                '<span class="input-group-addon">' .
                    zen_image(DIR_WS_CATALOG_LANGUAGES . $next_lang['directory'] . '/images/' . $next_lang['image'], $next_lang['name']) .
                '</span>';
            $titles .=
                '<div class="input-group">' .
                    $language_addon .
                    zen_draw_input_field('metatags_title[' . $lang_id . ']', htmlspecialchars(stripslashes($metatags[$lang_id]['metatags_title']), ENT_COMPAT, CHARSET, true), $title_field_length . ' class="form-control"') .
                '</div><br>';
            $keywords .=
                '<div class="input-group">' .
                    $language_addon .
                    zen_draw_textarea_field('metatags_keywords[' . $lang_id . ']', 'soft', '100', '3', htmlspecialchars(stripslashes($metatags[$lang_id]['metatags_keywords']), ENT_COMPAT, CHARSET, true), 'class="form-control"') .
                '</div><br>';
            $description .=
                '<div class="input-group">' .
                    $language_addon .
                    zen_draw_textarea_field('metatags_description[' . $lang_id . ']', 'soft', '100', '10', htmlspecialchars(stripslashes($metatags[$lang_id]['metatags_description']), ENT_COMPAT, CHARSET, true), 'class="form-control"') .
                '</div><br>';
        }
        return [
            ['text' => '<p class="p_label control-label">' . TEXT_EDIT_MANUFACTURER_META_TAGS_TITLE . '</p>' . $titles],
            ['text' => '<p class="p_label control-label">' . TEXT_EDIT_MANUFACTURER_META_TAGS_KEYWORDS . '</p>' . $keywords],
            ['text' => '<p class="p_label control-label">' . TEXT_EDIT_MANUFACTURER_META_TAGS_DESCRIPTION . '</p>' . $description],
        ];
    }
}
