<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle\Model;

use Contao\Model;

class StaffArchiveModel extends Model
{
    protected static $strTable = 'tl_staff_archive';

    public static function findPublishedByIds(array $arrPids, array $arrOptions = []): ?Model\Collection
    {
        if (empty($arrPids)) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = [$t . '.id IN(' . implode(',', array_map('\intval', $arrPids)) . ')'];

        return static::findBy($arrColumns, null, $arrOptions);
    }
}
