<?php

namespace App\Model\Repository\Base;


use App\Model\Repository\Domain\Allocation;
use DateTime;
use Nette\Database\Table\Selection;


/**
 * Přístup k datům z tabulky project_user_allocation
 */
interface IProjectUserAllocationRepository
{

    public function getTableName(): string;

    public function getAllocation(int $id): ?Allocation;


    public function saveAllocation(Allocation $allocation, int $projectUserId);

    /**
     * selekce pro grid
     * @param array $usersOnProjectIds
     * @return Selection
     */
    public function getAllAllocations(array $usersOnProjectIds): Selection;

    /**
     * Spocita pracovni zatizeni pracovnika v case
     * @param DateTime $from
     * @param DateTime $to
     * @param array $userProjectIds pole vsech clenstvi na projektech
     * @return int soucet v hodinach
     */
    public function getCurrentWorkload(DateTime $from, DateTime $to, array $userProjectIds): int;

    /**
     * Vrati soucet vsech alokaci
     * @param array $userProjectIds pole vsech clenstvi na projektech
     * @return int soucet v hodinach
     */
    public function getSumOfAllWorkload(array $userProjectIds ): int;

    /**
     * Kontrola, jestli je user s id projekt manager projektu, na kterem se tvori alokace
     * @param int $userId
     * @param int $allocationId
     * @return bool
     */
    public function isUserProjectManagerOfProjectOfThisAllocation(int $userId, int $allocationId): bool;



}