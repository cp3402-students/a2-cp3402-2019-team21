<?php

namespace GDGallery\Helpers;

use GDGallery\Helpers\View;
use GDGallery\Models\Gallery;

/**
 * Class SettingsPageBuilder
 * @package GDLightbox\Helpers
 */
class SettingsPageBuilder
{
    /**
     * @var string
     */
    private $pageTitle;

    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var array
     */
    private $sections = array();

    /**
     * @var array
     */
    private $tabs = array();

    /**
     * SettingsPageBuilder constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param string $pageTitle
     * @return SettingsPageBuilder
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
        return $this;
    }

    /**
     * @param $tabs array
     * @throws \Exception
     */
    public function addTabs($tabs)
    {
        if (empty($tabs) || !is_array($tabs)) {
            throw new \Exception('Trying to add empty tabs');
        }

        foreach ($tabs as $tab) {
            if (!isset($tab['title'])) {
                throw new \Exception('"title" parameter is required for sections');
            }
        }

        $this->tabs = array_merge($this->tabs, $tabs);
    }

    /**
     * @param $sections array
     * @throws \Exception
     */
    public function addSections($sections)
    {
        if (empty($sections) || !is_array($sections)) {
            throw new \Exception('Trying to add empty sections');
        }

        foreach ($sections as $section) {
            if (!isset($section['title'])) {
                throw new \Exception('"title" parameter is required for sections');
            }
        }

        $this->sections = array_merge($this->sections, $sections);
    }

    /**
     * @param $key string
     * @param $section array
     * @throws \Exception
     */
    public function addSection($key, $section)
    {
        if (empty($section) || !is_array($section)) {
            throw new \Exception('Trying to add empty section');
        }

        if (!isset($section['title'])) {
            throw new \Exception('"title" parameter is required for sections');
        }


        $this->sections[$key] = $section;
    }

    /**
     * @param $fields array
     * @throws \Exception
     */
    public function addFields($fields)
    {
        if (empty($fields) || !is_array($fields)) {
            throw new \Exception('Trying to add empty fields');
        }

        foreach ($fields as $field) {
            if (!isset($field['section'])) {
                throw new \Exception('"section" parameter is required for fields');
            }
        }

        $this->fields = array_merge($this->fields, $fields);
    }

    /**
     * @param $key string
     * @param $field array
     * @throws \Exception
     */
    public function addField($key, $field)
    {
        if (empty($field) || !is_array($field)) {
            throw new \Exception('Trying to add empty field');
        }

        if (!isset($field['section'])) {
            throw new \Exception('"section" parameter is required for fields');
        }

        $this->fields[$key] = $field;
    }

    public function render()
    {
        if (empty($this->sections) || empty($this->fields)) {
            throw new \Exception('Trying to render empty settings page, please add section(s) and field(s) first');
        }

        $tabs = $this->tabs;
        $sections = $this->sections;
        $fields = $this->fields;
        $title = (!empty($this->pageTitle) ? $this->pageTitle : '');

        View::render('admin/settings/tabs.php', compact('tabs', 'sections', 'fields', 'title'));
    }

    public function getOptions()
    {
        if (empty($this->sections) || empty($this->fields)) {
            throw new \Exception('Trying to render empty settings page, please add section(s) and field(s) first');
        }

        return array(
            "sections" => $this->sections,
            "fileds" => $this->fields,
            "title" => $this->title
        );

    }


}