<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle\Controller;

use Contao\BackendTemplate;
use Contao\CoreBundle\Exception\InternalServerErrorException;
use Contao\Input;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Studio7A\ContaoStaffBundle\Model\StaffEmployeeModel;

class ModuleStaffReader extends ModuleStaff
{
    protected $strTemplate = 'mod_staffreader';

    public function generate(): string
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . mb_strtoupper($GLOBALS['TL_LANG']['FMD']['staffreader'][0] ?? '') . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        if (!isset($_GET['items']) && isset($_GET['auto_item'])) {
            Input::setGet('items', Input::get('auto_item'));
        }

        if (!Input::get('items')) {
            return '';
        }

        $this->staff_archives = StringUtil::deserialize($this->staff_archives, true);

        if (empty($this->staff_archives)) {
            throw new InternalServerErrorException('The staff reader ID ' . $this->id . ' has no archives specified.');
        }

        return parent::generate();
    }

    protected function compile(): void
    {
        $this->Template->articles = '';

        if ($this->overviewPage) {
            $this->Template->referer = PageModel::findById($this->overviewPage)?->getFrontendUrl() ?? '';
            $this->Template->back = $this->customLabel ?: ($GLOBALS['TL_LANG']['MSC']['staffOverview'] ?? null);
        } else {
            $this->Template->referer = 'javascript:history.go(-1)';
            $this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
        }

        $objEmployee = StaffEmployeeModel::findPublishedByParentAndIdOrAlias(Input::get('items'), $this->staff_archives);

        if ($objEmployee === null) {
            return;
        }

        if (!$this->staff_template) {
            $this->staff_template = 'staff_full';
        }

        $arrEmployee = $this->parseEmployee($objEmployee);
        $this->Template->employees = $arrEmployee;
    }
}
