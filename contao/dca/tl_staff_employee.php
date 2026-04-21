<?php

use Contao\Backend;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;
use Studio7A\ContaoStaffBundle\Model\StaffArchiveModel;

System::loadLanguageFile('tl_content');

$GLOBALS['TL_DCA']['tl_staff_employee'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_staff_archive',
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid' => 'index',
                'published' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['sorting'],
            'headerFields' => ['title'],
            'panelLayout' => 'search,limit',
            'child_record_callback' => ['tl_staff_employee', 'generateEmployeeRow'],
        ],
        'global_operations' => [
            'departments' => [
                'label' => &$GLOBALS['TL_LANG']['tl_staff_employee']['departments'],
                'href' => 'table=tl_staff_department',
                'icon' => 'bundles/studio7acontaostaff/department.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="c"',
            ],
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'href' => 'act=toggle&amp;field=published',
                'icon' => 'visible.svg',
                'showInHeader' => true,
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],
    'palettes' => [
        'default' => '{title_legend},forename,surname,alias,namePrefix,nameAppendix,position,departments;' .
            '{photo_legend},singleSRC;' .
            '{description_legend},headline,description;' .
            '{data_legend},entryDate,phone,mobile,email,birthday;' .
            '{quote_legend:hide},quote;' .
            '{expert_legend:hide},cssClass;' .
            '{publish_legend},published,start,stop;',
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid' => [
            'foreignKey' => 'tl_staff_archive.title',
            'sql' => "int(10) unsigned NOT NULL default 0",
        ],
        'sorting' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'forename' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'surname' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'alias' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w50 clr'],
            'save_callback' => [
                ['tl_staff_employee', 'generateAlias'],
            ],
            'sql' => "varchar(255) BINARY NOT NULL default ''",
        ],
        'namePrefix' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50 clr'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'nameAppendix' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'position' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'departments' => [
            'exclude' => true,
            'sorting' => false,
            'filter' => true,
            'flag' => 1,
            'search' => true,
            'sql' => 'blob NULL',
            'foreignKey' => 'tl_staff_department.title',
            'inputType' => 'select',
            'eval' => [
                'includeBlankOption' => true,
                'multiple' => true,
                'chosen' => true,
                'tl_class' => 'w50',
            ],
        ],
        'entryDate' => [
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'doNotCopy' => true, 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => 'int(10) unsigned',
        ],
        'singleSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => '%contao.image.valid_extensions%'],
            'sql' => 'binary(16) NULL',
        ],
        'headline' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'description' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE'],
            'sql' => 'text NULL',
        ],
        'quote' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['allowHtml' => true],
            'sql' => 'text NULL',
        ],
        'phone' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'mobile' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'rgxp' => 'phone', 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'email' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'rgxp' => 'email', 'unique' => true, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'birthday' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'doNotCopy' => true, 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => 'int(10) unsigned',
        ],
        'cssClass' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'published' => [
            'exclude' => true,
            'toggle' => true,
            'filter' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'start' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'stop' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
    ],
];

class tl_staff_employee extends Backend
{
    public function generateEmployeeRow(array $arrRow): string
    {
        return '<div>' . $arrRow['forename'] . ' ' . $arrRow['surname'] . ' <span style="padding-left:3px;color:#b3b3b3;">[' . $arrRow['position'] . ']</span></div>';
    }

    public function generateAlias(mixed $varValue, DataContainer $dc): string
    {
        $aliasExists = function (string $alias) use ($dc): bool {
            return $this->Database->prepare("SELECT id FROM tl_staff_employee WHERE alias=? AND id!=?")->execute($alias, $dc->id)->numRows > 0;
        };

        if (!$varValue) {
            $varValue = System::getContainer()->get('contao.slug')->generate(
                $dc->activeRecord->forename . ' ' . $dc->activeRecord->surname,
                StaffArchiveModel::findByPk($dc->activeRecord->pid)?->jumpTo,
                $aliasExists
            );
        } elseif (preg_match('/^[1-9]\d*$/', $varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $varValue));
        } elseif ($aliasExists($varValue)) {
            throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
    }
}
