<?php

namespace App\Model\Repository\Base;




/**
 * Přístup k datům z tabulky superior_user
 */
interface ISuperiorUserRepository
{

    public function getTableName(): string;

    /**
     * Vrati vsechny podrizene k nadrizenemu
     * @param int $superiorId
     * @return array ve tvaru [id => workerId(id podrizeneho uzivatele)]
     */
    public function getAllSubordinates(int $superiorId): array;

    public function isSuperiorOfWorker(int $superiorId, int $workerId): bool;

    /**
     * ulozi data do db
     * @param int $superiorId
     * @param int $workerId
     * @return void
     */
    public function saveData(int $superiorId, int $workerId): void;


}