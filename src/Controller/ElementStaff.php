<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle\Controller;

use Contao\ContentElement;
use Contao\System;
use Studio7A\ContaoStaffBundle\Model\StaffEmployeeModel;

class ElementStaff extends ContentElement
{
    protected $strTemplate = 'ce_staff';

    public function generate(): string
    {
        $objEmployee = StaffEmployeeModel::findByPk($this->staff_employee);

        if ($objEmployee === null) {
            return '';
        }

        $this->employee = $objEmployee;

        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            return $objEmployee->forename . ' ' . $objEmployee->surname;
        }

        return parent::generate();
    }

    protected function compile(): void
    {
        if (!$this->employee) {
            return;
        }

        $objStaff = new ModuleStaffList($this->employee);
        $objStaff->staff_template = $this->staff_template ?: 'staff_short';
        $objStaff->imgSize = $this->size;

        if ($this->staff_jumpto) {
            $objStaff->jumpTo = $this->staff_jumpto;
        }

        $this->Template->html = $objStaff->parseEmployee($this->employee);
    }
}
