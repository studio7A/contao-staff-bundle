<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle\Controller;

use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\FrontendTemplate;
use Contao\Module;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Studio7A\ContaoStaffBundle\Model\StaffArchiveModel;
use Studio7A\ContaoStaffBundle\Model\StaffDepartmentModel;
use Studio7A\ContaoStaffBundle\Model\StaffEmployeeModel;

abstract class ModuleStaff extends Module
{
    protected function sortOutProtected(array $arrArchives): array
    {
        if (empty($arrArchives)) {
            return $arrArchives;
        }

        $objArchive = StaffArchiveModel::findMultipleByIds($arrArchives);
        $arrArchives = [];

        if ($objArchive !== null) {
            $security = System::getContainer()->get('security.helper');

            while ($objArchive->next()) {
                if ($objArchive->protected && !$security->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, StringUtil::deserialize($objArchive->memberGroups, true))) {
                    continue;
                }

                $arrArchives[] = $objArchive->id;
            }
        }

        return $arrArchives;
    }

    public function parseEmployees($objEmployees): array
    {
        $arrEmployees = [];

        if ($this->staffFilterDepartments) {
            $arrDepartmentsSetting = StringUtil::deserialize($this->staff_departments) ?: [];
        }

        foreach ($objEmployees as $objEmployee) {
            $booAddEmployee = true;

            if ($this->staffFilterDepartments) {
                $arrDepartments = StringUtil::deserialize($objEmployee->departments) ?: [];

                if (!array_intersect($arrDepartments, $arrDepartmentsSetting)) {
                    $booAddEmployee = false;
                }
            }

            if ($booAddEmployee) {
                $arrEmployees[] = $this->parseEmployee($objEmployee);
            }
        }

        return $arrEmployees;
    }

    public function parseEmployee($objEmployee, string $strClass = ''): string
    {
        $objTemplate = new FrontendTemplate($this->staff_template ?: 'staff_short');
        $objTemplate->setData($objEmployee->row());

        if ($objEmployee->cssClass) {
            $strClass = ' ' . $objEmployee->cssClass . $strClass;
        }

        $objTemplate->class = $strClass;

        if ($objEmployee->departments) {
            $arrDepartments = StringUtil::deserialize($objEmployee->departments);
            $objDepartments = StaffDepartmentModel::findMultipleByIds($arrDepartments);
            $arrDepartmentsTitles = [];

            if ($objDepartments !== null) {
                foreach ($objDepartments as $objDepartment) {
                    $arrDepartmentsTitles[] = $objDepartment->title;
                }
            }

            $objTemplate->department = $arrDepartmentsTitles;
        }

        if (isset($this->jumpTo) && $this->jumpTo != 0) {
            $objPage = PageModel::findByPk($this->jumpTo);

            if ($objPage !== null) {
                $strParams = '/' . ($objEmployee->alias ?: $objEmployee->id);
                $objTemplate->link = $objPage->getFrontendUrl($strParams);
            }
        }

        $figureBuilder = System::getContainer()
            ->get('contao.image.studio')
            ->createFigureBuilder()
            ->from($objEmployee->singleSRC)
            ->setSize($this->imgSize);

        if (null !== ($figure = $figureBuilder->buildIfResourceExists())) {
            $figure->applyLegacyTemplateData($objTemplate, $objEmployee->imagemargin, $objEmployee->floating);
        }

        $objTemplate->getSchemaOrgData = static function () use ($objTemplate, $objEmployee): array {
            $jsonLd = StaffFrontend::getSchemaOrgData($objEmployee);

            if ($objTemplate->addImage && $objTemplate->figure) {
                $jsonLd['image'] = $objTemplate->figure->getSchemaOrgData();
            }

            return $jsonLd;
        };

        return $objTemplate->parse();
    }
}
