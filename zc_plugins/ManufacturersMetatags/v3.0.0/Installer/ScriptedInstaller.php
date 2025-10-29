<?php
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    /**
     * @return bool
     */
    protected function executeInstall()
    {
        global $sniffer;
        if (!$sniffer->field_exists(TABLE_MANUFACTURERS_INFO, 'metatags_title')) {
            $this->executeInstallerSql(
                "ALTER TABLE " . TABLE_MANUFACTURERS_INFO . "
                    ADD COLUMN metatags_title VARCHAR(255) NOT NULL DEFAULT '',
                    ADD COLUMN metatags_keywords TEXT,
                    ADD COLUMN metatags_description TEXT"
            );
        }
        parent::executeInstall();

        zen_define_default('TABLE_MANUFACTURERS_META', DB_PREFIX . 'manufacturers_meta');
        if ($sniffer->table_exists(TABLE_MANUFACTURERS_META)) {
            $existing = $this->dbConn->Execute(
                "SELECT *
                   FROM " . TABLE_MANUFACTURERS_META
            );
            foreach ($existing as $next_metatag) {
                $this->executeInstallerSql(
                    "UPDATE " . TABLE_MANUFACTURERS_INFO . "
                        SET metatags_title = '" . $next_metatag['metatags_title'] . "',
                            metatags_keywords = '" . $next_metatag['metatags_keywords'] . "',
                            metatags_description = '" . $next_metatag['metatags_description'] . "'
                      WHERE manufacturers_id = " . (int)$next_metatag['manufacturers_id'] . "
                        AND languages_id = " . (int)$next_metatag['language_id'] . "
                      LIMIT 1"
                );
            }
        }

        return true;
    }

    // -----
    // Note: This (https://github.com/zencart/zencart/pull/6498) Zen Cart PR must
    // be present in the base code or a PHP Fatal error is generated due to the
    // function signature difference.
    //
    protected function executeUpgrade($oldVersion)
    {
        parent::executeUpgrade($oldVersion);
    }

    /**
     * @return bool
     */
    protected function executeUninstall()
    {
        parent::executeUninstall();
        return true;
    }
}
