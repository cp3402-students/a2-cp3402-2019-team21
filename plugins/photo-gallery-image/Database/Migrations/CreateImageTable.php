<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 7/3/2017
 * Time: 4:09 PM
 */

namespace GDGallery\Database\Migrations;

use GDGallery\Models\Gallery as Gallery;

class CreateImageTable
{
    public static function run()
    {
        global $wpdb;

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS " . Gallery::getItemsTableName() . "(
                `id_image` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_gallery` int(11) UNSIGNED NOT NULL,
                `id_post` int(11) UNSIGNED  NULL DEFAULT 0,
                `name` varchar(255) NULL,
                `description` text,
                `ordering` int(11) NOT NULL DEFAULT 0,
                `link` varchar(255) NULL,
                `url` varchar(255) NOT NULL,
                `target` ENUM('_blank','_self','_top','_parent') DEFAULT '_blank',
                `type` ENUM('image', 'youtube', 'vimeo') DEFAULT 'image',
                `ctime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `video_id` varchar(50) NULL,
                PRIMARY KEY (id_image),
				FOREIGN KEY (id_gallery) REFERENCES " . Gallery::getTableName() . " (id_gallery) ON DELETE CASCADE
            ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci "
        );
    }
}