<?php

use Contao\Controller;
use Contao\Database;

$GLOBALS['TL_DCA']['tl_content']['palettes']['staff'] = '{type_legend},type,headline;' .
    '{employee_legend},staff_employee;' .
    '{image_legend},size;' .
    '{page_legend},staff_jumpto;' .
    '{protected_legend:hide},protected;' .
    '{expert_legend:hide},guests,cssID;' .
    '{template_legend:hide},staff_template,customTpl;' .
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['staff_employee'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['staff_employee'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'select',
    'options_callback' => ['tl_content_staff', 'getEmployees'],
    'eval' => ['mandatory' => true, 'chosen' => true, 'submitOnChange' => true, 'tl_class' => 'w50 wizard'],
    'sql' => "int(10) unsigned NOT NULL default 0",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['staff_jumpto'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['staff_jumpto'],
    'exclude' => true,
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'w50 clr'],
    'sql' => "int(10) unsigned NOT NULL default 0",
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['staff_template'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_module']['staff_template'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => static function () {
        return Controller::getTemplateGroup('staff_');
    },
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(64) COLLATE ascii_bin NOT NULL default ''",
];

class tl_content_staff
{
    public function getEmployees(): array
    {
        $arrEmployees = [];
        $objEmployees = Database::getInstance()->execute("SELECT id, forename, surname FROM tl_staff_employee ORDER BY surname");

        while ($objEmployees->next()) {
            $arrEmployees[$objEmployees->id] = $objEmployees->forename . ' ' . $objEmployees->surname;
        }

        return $arrEmployees;
    }
}
