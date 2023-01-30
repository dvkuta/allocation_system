<?php
namespace App\Tools;

use App\Model\Project\ProjectUser\EState;
use UnitEnum;

/**
 * Trida s pomocnymi funkcionalitami
 */
class Utils
{
    /**
     * Transformuje ID do pozadovane podoby
     * @param string|null $id
     * @return int|null
     */
    public static function transformId(?string $id = null): ?int
    {
        if($id === null) return null;

        if(empty($id)) return null;

        if(!is_numeric($id)) return null;

        return intval($id);
    }

    /**
     * Konvertuje hodnoty enumu do pole
     * @param array $enums
     * @return array
     */
    public static function getEnumValuesAsArray(array $enums): array
    {
        $resultArray = [];
        foreach ($enums as $case)
        {
            $resultArray[$case->value] = $case->value;
        }
        return $resultArray;
    }

    /**
     * Vrati retezec reprezentujici alokaci i s FTE
     * @param int $allocation
     * @param int $max_allocation
     * @return string
     */
    public static function getAllocationString(int $allocation, int $max_allocation): string
    {
        return $allocation . "h (FTE: ". $allocation / $max_allocation. ")";
    }

}