<?php

declare(strict_types=1);

namespace Studio7A\ContaoStaffBundle\Model;

use Contao\Date;
use Contao\Model;

class StaffEmployeeModel extends Model
{
    protected static $strTable = 'tl_staff_employee';

    public static function findPublishedByParentAndIdOrAlias(string $varId, array $arrPids, array $arrOptions = []): ?self
    {
        if (empty($arrPids) || !\is_array($arrPids)) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = !preg_match('/^[1-9]\d*$/', $varId)
            ? ['BINARY ' . $t . '.alias=?']
            : [$t . '.id=?'];
        $arrColumns[] = $t . '.pid IN(' . implode(',', array_map('\intval', $arrPids)) . ')';

        if (!static::isPreviewMode($arrOptions)) {
            $time = Date::floorToMinute();
            $arrColumns[] = "$t.published='1' AND ($t.start='' OR $t.start<=$time) AND ($t.stop='' OR $t.stop>$time)";
        }

        return static::findOneBy($arrColumns, $varId, $arrOptions);
    }

    public static function findByParent(int $intPid): ?Model\Collection
    {
        $t = static::$strTable;
        $arrColumns = [$t . ".pid=? AND $t.published='1'"];
        $arrOptions = ['order' => $t . '.sorting'];

        return static::findBy($arrColumns, [$intPid], $arrOptions);
    }

    public static function findPublishedByPids(array $arrPids, array $arrOptions = []): ?Model\Collection
    {
        if (empty($arrPids) || !\is_array($arrPids)) {
            return null;
        }

        $t = static::$strTable;
        $arrColumns = [$t . '.pid IN(' . implode(',', array_map('\intval', $arrPids)) . ')'];

        if (!static::isPreviewMode($arrOptions)) {
            $time = Date::floorToMinute();
            $arrColumns[] = "$t.published='1' AND ($t.start='' OR $t.start<=$time) AND ($t.stop='' OR $t.stop>$time)";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = $t . '.sorting ASC';
        }

        return static::findBy($arrColumns, null, $arrOptions);
    }
}
