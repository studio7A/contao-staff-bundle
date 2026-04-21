<?php

use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'staffFilterDepartments';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['staffFilterDepartments'] = 'staff_departments';

$GLOBALS['TL_DCA']['tl_module']['palettes']['stafflist'] = '{title_legend},name,headline,type;' .
    '{config_legend},staff_archives,staffFilterDepartments,staff_order,staff_description;' .
    '{redirect_legend},jumpTo;' .
    '{protected_legend:hide},protected;' .
    '{template_legend:hide},staff_template,customTpl;' .
    '{image_legend:hide},imgSize;' .
    '{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['staffreader'] = '{title_legend},name,headline,type;' .
    '{config_legend},staff_archives,overviewPage,customLabel;' .
    '{protected_legend:hide},protected;' .
    '{template_legend:hide},staff_template,customTpl;' .
    '{image_legend:hide},imgSize;' .
    '{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['staff_archives'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['staff_archives'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'options_callback' => ['tl_module_staff', 'getStaffArchives'],
    'eval' => ['multiple' => true, 'mandatory' => true],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['staffFilterDepartments'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['staff_departments'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['staff_departments'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'options_callback' => ['tl_module_staff', 'getStaffDepartments'],
    'eval' => ['multiple' => true],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['staff_description'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['staff_description'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => ['style' => 'height:60px', 'decodeEntities' => true, 'tl_class' => 'clr', 'rte' => 'tinyMCE'],
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['staff_order'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['staff_order'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => ['tl_module_staff', 'getSortingOptions'],
    'reference' => &$GLOBALS['TL_LANG']['tl_module'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(32) COLLATE ascii_bin NOT NULL default 'order_date_desc'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['staff_template'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['staff_template'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => static function () {
        return Controller::getTemplateGroup('staff_');
    },
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(64) COLLATE ascii_bin NOT NULL default ''",
];

class tl_module_staff
{
    public function getStaffArchives(): array
    {
        $arrArchives = [];
        $objArchive = Database::getInstance()->execute("SELECT id, title FROM tl_staff_archive ORDER BY title");

        while ($objArchive->next()) {
            $arrArchives[$objArchive->id] = $objArchive->title;
        }

        return $arrArchives;
    }

    public function getStaffDepartments(): array
    {
        $arrDepartments = [];
        $objDepartment = Database::getInstance()->execute("SELECT id, title FROM tl_staff_department ORDER BY title");

        while ($objDepartment->next()) {
            $arrDepartments[$objDepartment->id] = $objDepartment->title;
        }

        return $arrDepartments;
    }

    public function getSortingOptions(DataContainer $dc): array
    {
        return ['order_sorting_asc', 'order_sorting_desc', 'order_entrydate_asc', 'order_entrydate_desc', 'order_surname_asc', 'order_surname_desc', 'order_random'];
    }
}
