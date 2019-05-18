<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 7/4/2017
 * Time: 10:31 AM
 */

namespace GDGallery\Models;

use GDGallery\Core\Model;

class Gallery extends Model
{
    protected static $tableName = 'gdgallerygalleries';

    protected static $itemsTableName = 'gdgalleryimages';

    /**
     * Form Name
     *
     * @var string
     */
    private $Name;

    private $Items;

    private $Gallery;

    private $View_style;

    private $cache = array();

    private $DisplayTitle;

    protected static $dbFields = array(
        'name', 'description', 'display_type', 'ordering', 'display_type', 'view_type', 'position', 'hover_effect', 'items_per_page', 'custom_css'
    );

    public function __construct($args = array())
    {

        if (isset($args["id_gallery"])) {
            $this->setID($args["id_gallery"]);
        }

//        $this->setViewStyles();

        parent::__construct($args);


        if (null !== $this->id_gallery) {

            $this->Gallery = $this->getGallery();

            if (!isset($this->Gallery)) {
                die("Undefined Gallery ID");
            }
            $this->Name = $this->Gallery->name;


        } else {
            $this->Name = __('New Gallery', GDGALLERY_TEXT_DOMAIN);

            $this->DisplayTitle = 1;
        }

    }


    public static function getTableName()
    {
        return $GLOBALS['wpdb']->prefix . self::$tableName;
    }

    public static function getItemsTableName()
    {
        return $GLOBALS['wpdb']->prefix . self::$itemsTableName;
    }


    public function setId($id)
    {
        $this->id_gallery = $id;
    }

    public function getId()
    {
        return $this->id_gallery;
    }

    public function unsetId()
    {
        $this->id_gallery = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return (!empty($this->Name) ? $this->Name : __('New Created Gallery', GDGALLERY_TEXT_DOMAIN));
    }

    /**
     * @param string $name
     *
     * @return Gallery
     */
    public function setName($name)
    {
        $this->Name = sanitize_text_field($name);

        return $this;
    }

    /**
     * Edit link for current gallery
     */
    public function getEditLink()
    {

        if (is_null($this->id_gallery)) {
            return false;
        }

        return admin_url('admin.php?page=gdgallery&task=edit_gallery&id=' . $this->id_gallery);

    }

    /**
     * @return Field[]
     */
    public function getItems($is_admin = false)
    {
        global $wpdb;

        $order_inf = $this->getOrderInfo();


        if ($is_admin === true) {
            $query = $wpdb->prepare("select * from `" . self::getItemsTableName() . "` where id_gallery=%d order by ordering ASC ", $this->id_gallery);
        } else {
            if($order_inf["order"] == 'RAND') {
                $query = $wpdb->prepare("select * from `" . self::getItemsTableName() . "` where id_gallery=%d order by RAND()", $this->id_gallery);
            }
            else {
                $query = $wpdb->prepare("select * from `" . self::getItemsTableName() . "` where id_gallery=%d order by " . $order_inf["sort"] . " " . $order_inf["order"], $this->id_gallery);
            }
        }
        $items = $wpdb->get_results($query);

        foreach ($items as $key => $val) {
            if ($val->id_post != 0) {
                $post = get_post($val->id_post);
                $items[$key]->url = wp_get_attachment_url($post->ID);
                $items[$key]->name = $val->name;
            }
        }

        if (empty($items)) {
            return null;
        }

        $this->Items = $items;
        return $this->Items;
    }


    /**
     * @return Field[]
     */
    public function getItemsPerPage($data)
    {
        global $wpdb;

        $order_inf = $this->getOrderInfo();

        if ($data->items_per_page) {
            $num = $data->items_per_page;
        } else {
            $num = 999;
        }

        $items_count = $this->getItemsCount();

        $total = intval((($items_count - 1) / $num) + 1);

        if (isset($_GET["gdgallery-page"])) {
            $page = absint($_GET["gdgallery-page"]);
        } else {
            $page = '';
        }
        if (empty($page) or $page < 0) {
            $page = 1;
        }
        if ($page > $total) {
            $page = $total;
        }
        $start = $page * $num - $num;
        if($order_inf["order"] == 'RAND') {
            $query = $wpdb->prepare("select * from `" . self::getItemsTableName() . "` where id_gallery=%d order by RAND()" . " LIMIT " . $start . "," . $num, $this->id_gallery);
        } else {
            $query = $wpdb->prepare("SELECT * FROM " . self::getItemsTableName() . " where id_gallery = '%d' order by " . $order_inf["sort"] . " " . $order_inf["order"] . " LIMIT " . $start . "," . $num, $this->id_gallery);
        }
        $items = $wpdb->get_results($query);

        foreach ($items as $key => $val) {
            if ($val->id_post != 0) {
                $post = get_post($val->id_post);
                $items[$key]->url = wp_get_attachment_url($post->ID);
//                $items[$key]->name = $post->post_title;
                $items[$key]->name = $val->name;
            }
        }

        if (empty($items)) {
            return null;
        }

        $this->Items = $items;

        return $this->Items;
    }

    public function duplicateGallery()
    {
        $gallery_data = (array)$this->getGallery(true);
        $gallery_items = (array)$this->getItems();
        unset($gallery_data["id_gallery"]);
        $gallery_data["name"] = $this->getName();
        array_walk($gallery_items, function ($item) {
            unset($item->id_image);
        });

        $id_gallery = $this->AddDuplicatedData($gallery_data, $gallery_items);

        return $id_gallery;
    }

    public function AddDuplicatedData($gallery, $items)
    {
        global $wpdb;

        $wpdb->insert(self::getTableName(), $gallery);
        $id_gallery = $wpdb->insert_id;

//        $id_gallery = 12;
        array_walk($items, function ($item) use ($id_gallery) {
            $item->id_gallery = $id_gallery;
        });
        foreach ($items as $item) {
            $wpdb->insert(self::getItemsTableName(), (array)$item);
        }

        return $id_gallery;
    }


    public
    function getGallery($duplicate = false)
    {
        global $wpdb;

        $query = $wpdb->prepare("select * from `" . self::getTableName() . "` where id_gallery=%d order by ordering", $this->id_gallery);
        $galleries = $wpdb->get_row($query);



        if (empty($galleries)) {
            return;
        }

        if ($duplicate == false) {
            $total = 0;
            if ($galleries->display_type == 2) {
                $items_count = $this->getItemsCount();
                $total = intval((($items_count - 1) / $galleries->items_per_page) + 1);
            }
            $galleries->total = $total;
        }


        if (empty($galleries)) {
            return null;
        }

        $this->Gallery = $galleries;


        return $this->Gallery;
    }

    public function getGalleriesUrl()
    {
        global $wpdb;
        $list = array();

        $query = "select `id_gallery`,`name` from `" . self::getTableName() . "` order by ordering";
        $galleries = $wpdb->get_results($query);
        foreach ($galleries as $val) {
            $EditUrl = admin_url('admin.php?page=gdgallery&task=edit_gallery&id=' . $val->id_gallery);
            $EditUrl = wp_nonce_url($EditUrl, 'gdgallery_edit_gallery_' . $val->id_gallery);
            $list[] = array(
                "id_gallery" => $val->id_gallery,
                "name" => $val->name,
                "url" => $EditUrl
            );
        }

        return $list;
    }

    public
    function saveGallery($data)
    {
        global $wpdb;
        $result = $wpdb->update(static::getTableName(), $data, array(static::$primaryKey => $data["id_gallery"]));

        if (false !== $result) {
            return static::$primaryKey;
        }

        return false;
    }

    public
    function saveGalleryImages($data)
    {
        global $wpdb;


        foreach ($data as $key => $val) {
            if ($key != "id_gallery") {
                foreach ($val as $k => $v) {
                    $wpdb->update(self::getItemsTableName(), array($key => $v), array(static::$primaryKey => $data["id_gallery"], "id_image" => $k));
                }
            }
        }
        return static::$primaryKey;
    }

    public function updateImageOrdering($arr)
    {
        global $wpdb;

        foreach ($arr as $key => $val) {
            $wpdb->update(self::getItemsTableName(), array("ordering" => $val), array("id_image" => $key));
        }
    }

    public function removeGalleryItems($data)
    {
        global $wpdb;

        foreach ($data as $key => $val) {
            $wpdb->delete(self::getItemsTableName(), array('id_image' => $val));
        }
        return static::$primaryKey;
    }

    public
    function AddGalleryImage($images, $id_gallery)
    {
        global $wpdb;
        $last_image_order = $this->getItemsCount();

        foreach ($images as $img) {
            $wpdb->insert(self::getItemsTableName(), array(
                    "id_gallery" => $id_gallery,
                    "id_post" => intval($img["id"]),
                    "name" => esc_html($img["name"]),
                    'url' => esc_sql($img["url"]),
                    "ordering" => 0,
                    "target" => "_blank",
                    "type" => "image"
                )
            );
        }
        return static::$primaryKey;
    }


    public function EditGalleryThumbnail($data, $id_image)
    {
        global $wpdb;

        $image = end($data);

        $result = $wpdb->update(
            self::getItemsTableName(),
            array("id_post" => $image["id"]),
            array("id_image" => $id_image)
        );

        return $result;

    }


    public
    function AddGalleryVideo($data)
    {
        global $wpdb;

        $type = self::getVideoType($data["gdgallery_video_url"]);
        $video_id = self::getVideoId($data["gdgallery_video_url"], $type);
        $url = self::getVideoThumb($video_id, $type);
        $last_image_order = $this->getItemsCount();

        if ($type === false) {
            $type = "image";
        }

        $wpdb->insert(self::getItemsTableName(), array(
                "id_gallery" => $data["gdgallery_id_gallery"],
                "name" => $data["gdgallery_video_name"],
                "description" => $data["gdgallery_video_description"],
                'url' => esc_url($url),
                "ordering" => 0,
                "link" => $data["gdgallery_video_link"],
                "target" => $data["gdgallery_video_target"],
                "type" => $type,
                "video_id" => $video_id
            )
        );
        return static::$primaryKey;
    }

    public
    function setViewStyles()
    {
        $this->View_style = array(
            array("Jastified", GDGALLERY_IMAGES_URL . "icons/view/jastified_gray.png"),
            array("Tiles", GDGALLERY_IMAGES_URL . "icons/view/tiles_gray.png"),
            array("Carousel", GDGALLERY_IMAGES_URL . "icons/view/carousel_gray.png"),
            array("Slider", GDGALLERY_IMAGES_URL . "icons/view/slider_gray.png"),
            array("Grid", GDGALLERY_IMAGES_URL . "icons/view/grid_gray.png")
        );

    }

    public
    function getViewStyles()
    {
        return $this->View_style;
    }

    /**
     * return string 0|1
     */
    public
    function getDisplayTitle()
    {
        return $this->DisplayTitle;
    }

    /**
     * @param $value int 0,1
     * @return $this
     */
    public
    function setDisplayTitle($value)
    {
        if (in_array($value, array(0, 1, 'on'))) {
            if ($value == 'on') $value = 1;

            $this->DisplayTitle = intval($value);
        }
        return $this;
    }

    /**
     * @param $key string
     * @param mixed $default
     * @return mixed
     */
    public
    function getData($key, $default = false)
    {
        if (!in_array($key, $this->cache)) {
            global $wpdb;
            $value = $wpdb->get_var($wpdb->prepare('select Value from ' . self::getTableName() . ' where Name=%s', $key));

            if (empty($value)) {
                $this->$key = $default;
            } else {
                $unserialized_value = @unserialize($value);

                if (false !== $unserialized_value || 'b:0;' === $value) {
                    $value = $unserialized_value;
                }

                $this->$key = $value;
            }


            $this->cache[] = $key;

        }

        return $this->$key;
    }


    /**
     * @return array
     */
    public function getOrderInfo()
    {
        $gallery_data = $this->getGallery();
        switch ($gallery_data->sort_by) {
            case 0:
                $sort = "ordering";
                break;
            case 1:
                $sort = "name";
                break;
            case 2:
                $sort = "ctime";
                break;
            default:
                $sort = "ordering";
        }
        switch ($gallery_data->order_by) {
            case 0:
                $order = "ASC";
                break;
            case 1:
                $order = "DESC";
                break;
            case 2:
                $order = "RAND";
                break;
            default:
                $order = "ASC";
        }

        return array("sort" => $sort, "order" => $order);

    }


    /**
     * @return int
     */
    public function getItemsCount()
    {
        global $wpdb;

        $query = $wpdb->prepare("select COUNT(*) AS count from `" . self::getItemsTableName() . "` where id_gallery=%d", $this->id_gallery);
        return $wpdb->get_var($query);
    }

    /**
     * @param $key string
     * @param $value string
     * @return bool
     */
    public
    function set($key, $value)
    {
        global $wpdb;

        $option_exists = $this->getData($key);

        if ($option_exists) {
            $saved = $wpdb->update(self::getTableName(),
                array('Value' => esc_sql($value)),
                array('Name' => esc_sql($key))
            );
        } else {
            $saved = $wpdb->insert(self::getTableName(), array(
                    'Value' => esc_sql($value),
                    'Name' => esc_sql($key)
                )
            );
        }

        $this->$key = $value;

        return (bool)$saved;
    }

    public static function getVideoType($url)
    {
        if (strpos($url, "youtube") !== false || strpos($url, "youtu.be") !== false) {
            return "youtube";
        } elseif (strpos($url, "vimeo") !== false) {
            return "vimeo";
        }

        return false;
    }

    public static function getVideoId($url, $type)
    {
        $video_id = null;
        if ($type == "youtube") {
            $video_id = substr($url, -11);
        } elseif ($type == "vimeo") {
            $video_id = substr($url, -9);
            if (strpos($video_id, '/') !== false) {
                $video_id = str_replace("/", "", $video_id);
            }
        }


        return $video_id;
    }

    public static function getVideoThumb($video_id, $type)
    {

        $thumbnail = null;

        if ($type == "youtube") {
            $thumbnail = "https://img.youtube.com/vi/" . $video_id . "/0.jpg";
        } elseif ($type == "vimeo") {
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$video_id.php"));
            $thumbnail = $hash[0]['thumbnail_medium'];
        }

        return $thumbnail;

    }

}