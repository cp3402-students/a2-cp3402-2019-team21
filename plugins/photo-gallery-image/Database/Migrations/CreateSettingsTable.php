<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 7/10/2017
 * Time: 2:13 PM
 */

namespace GDGallery\Database\Migrations;


class CreateSettingsTable
{
    /**
     * Run the migration
     */
    public static function run()
    {
        global $wpdb;

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "gdgallerysettings`(
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `option_key` VARCHAR(200) NOT NULL,
                `option_value` TEXT,
                 PRIMARY KEY(`id`),
                 UNIQUE KEY(`option_key`)
            ) CHARACTER SET utf8 COLLATE utf8_general_ci"
        );
    }
}