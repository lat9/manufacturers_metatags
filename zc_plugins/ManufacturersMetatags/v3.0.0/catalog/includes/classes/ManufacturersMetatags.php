<?php
// -----
// Part of the Manufacturers Metatags plugin by lat9 (lat9@vinosdefrutastropicales.com).
// Copyright (C) 2025, Vinos de Frutas Tropicales
//
// Last updated: v3.0.0
//
namespace Zencart\Plugins\Catalog\ManufacturersMetatags;

use Zencart\Traits\NotifierManager;

class ManufacturersMetatags
{
    protected array $metaTags = [];
    protected array $defaultMetaTags;

    public function __construct(int $manufacturers_id = 0)
    {
        global $lng;
        $languages = $this->getLanguagesById();
        $this->defaultMetaTags = [
            'metatags_title' => '',
            'metatags_keywords' => '',
            'metatags_description' => '',
        ];
        if ($manufacturers_id === 0) {
            foreach ($languages as $lang_id => $next_lang) {
                $this->metaTags[$lang_id] = $this->defaultMetaTags;
            }
            return;
        }

        global $db;
        $metatags = $db->Execute(
            "SELECT *
               FROM " . TABLE_MANUFACTURERS_INFO . "
              WHERE manufacturers_id = $manufacturers_id"
        );
        foreach ($metatags as $next_tag) {
            $next_tag['metatags_keywords'] = (string)$next_tag['metatags_keywords'];
            $next_tag['metatags_description'] = (string)$next_tag['metatags_description'];
            $this->metaTags[$next_tag['languages_id']] = $next_tag;
            unset($languages[$next_tag['languages_id']]);
        }
        if (count($languages) === 0) {
            return;
        }
        foreach ($languages as $lang_id => $next_lang) {
            $this->metaTags[$lang_id] = $this->defaultMetaTags;
        }
    }

    protected function getLanguagesById(): array
    {
        global $lng;
        if (isset($lng)) {
            return $lng->get_languages_by_id();
        }
        
        global $languages;
        $lang_by_id = [];
        foreach ($languages as $next_lang) {
            $lang_by_id[$next_lang['id']] = $next_lang;
        }
        return $lang_by_id;
    }

    public function getAllMetaTags(): array
    {
        return $this->metaTags;
    }

    public function getMetaTagsForLanguage(int $language_id): array
    {
        return $this->metaTags[$language_id] ?? $this->defaultMetaTags;
    }
}
