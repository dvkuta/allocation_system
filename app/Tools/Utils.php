<?php
namespace App\Tools;

class Utils
{
    public static function transformId(?string $id = null): ?int
    {
        if($id === null) return null;

        if(empty($id)) return null;

        if(!is_numeric($id)) return null;

        return intval($id);
    }

}