<?php

use Studio7A\ContaoStaffBundle\Controller\ElementStaff;
use Studio7A\ContaoStaffBundle\Controller\ModuleStaffList;
use Studio7A\ContaoStaffBundle\Controller\ModuleStaffReader;
use Studio7A\ContaoStaffBundle\Model\StaffArchiveModel;
use Studio7A\ContaoStaffBundle\Model\StaffDepartmentModel;
use Studio7A\ContaoStaffBundle\Model\StaffEmployeeModel;

array_insert($GLOBALS['BE_MOD']['content'], 1, [
    'staff' => [
        'tables' => ['tl_staff_archive', 'tl_staff_employee', 'tl_staff_department'],
    ],
]);

$GLOBALS['FE_MOD']['miscellaneous']['stafflist'] = ModuleStaffList::class;
$GLOBALS['FE_MOD']['miscellaneous']['staffreader'] = ModuleStaffReader::class;

$GLOBALS['TL_MODELS']['tl_staff_archive'] = StaffArchiveModel::class;
$GLOBALS['TL_MODELS']['tl_staff_employee'] = StaffEmployeeModel::class;
$GLOBALS['TL_MODELS']['tl_staff_department'] = StaffDepartmentModel::class;

$GLOBALS['TL_CTE']['includes']['staff'] = ElementStaff::class;
