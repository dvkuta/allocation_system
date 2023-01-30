<?php
namespace App\Model\Repository\Base;

/**
 * Přístup k datům z tabulky Role
 */
interface IRoleRepository
{

    public function getTableName(): string;

    /**
     * Vrati data pro select box ve tvaru
     * @return array [id => typ_role]
     */
    public function fetchDataForSelect(): array;

}
