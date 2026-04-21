<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle\Controller;

use Contao\Frontend;
use Contao\System;
use Studio7A\ContaoStaffBundle\Model\StaffEmployeeModel;

class StaffFrontend extends Frontend
{
    public static function getSchemaOrgData(StaffEmployeeModel $objEmployee): array
    {
        $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');

        $jsonLd = [
            '@type' => 'Person',
            'identifier' => '#/schema/person/' . $objEmployee->id,
            'givenName' => $htmlDecoder->inputEncodedToPlainText($objEmployee->forename),
            'familyName' => $htmlDecoder->inputEncodedToPlainText($objEmployee->surname),
            'jobTitle' => $htmlDecoder->inputEncodedToPlainText($objEmployee->position),
            'birthDate' => $objEmployee->year_of_birth,
        ];

        if ($objEmployee->description) {
            $jsonLd['description'] = $htmlDecoder->htmlToPlainText($objEmployee->description);
        }

        return $jsonLd;
    }
}
