<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle\Controller;

use Contao\BackendTemplate;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Studio7A\ContaoStaffBundle\Model\StaffEmployeeModel;

class ModuleStaffList extends ModuleStaff
{
    protected $strTemplate = 'mod_stafflist';

    public function generate(): string
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . mb_strtoupper($GLOBALS['TL_LANG']['FMD']['stafflist'][0] ?? '') . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $this->staff_archives = $this->sortOutProtected(StringUtil::deserialize($this->staff_archives, true));

        if (empty($this->staff_archives) || !\is_array($this->staff_archives)) {
            return '';
        }

        return parent::generate();
    }

    protected function compile(): void
    {
        $t = StaffEmployeeModel::getTable();
        $order = '';

        switch ($this->staff_order) {
            case 'order_sorting_asc':
                $order = "$t.sorting";
                break;
            case 'order_sorting_desc':
                $order = "$t.sorting DESC";
                break;
            case 'order_surname_asc':
                $order = "$t.surname";
                break;
            case 'order_surname_desc':
                $order = "$t.surname DESC";
                break;
            case 'order_entrydate_asc':
                $order = "$t.entryDate";
                break;
            case 'order_entrydate_desc':
                $order = "$t.entryDate DESC";
                break;
            case 'order_random':
                $order = 'RAND()';
                break;
            default:
                $order = "$t.sorting ASC";
        }

        $objEmployees = StaffEmployeeModel::findPublishedByPids($this->staff_archives, ['order' => $order]);

        if ($objEmployees !== null) {
            $this->Template->description = $this->staff_description;
            $this->Template->employees = $this->parseEmployees($objEmployees);
        }
    }
}
