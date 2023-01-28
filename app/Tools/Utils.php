<?php
namespace App\Tools;

use App\Model\Project\ProjectUser\EState;
use UnitEnum;

class Utils
{
    public static function transformId(?string $id = null): ?int
    {
        if($id === null) return null;

        if(empty($id)) return null;

        if(!is_numeric($id)) return null;

        return intval($id);
    }

    public static function getEnumValuesAsArray(array $enums): array
    {
        $resultArray = [];
        foreach ($enums as $case)
        {
            $resultArray[$case->value] = $case->value;
        }
        return $resultArray;
    }

}