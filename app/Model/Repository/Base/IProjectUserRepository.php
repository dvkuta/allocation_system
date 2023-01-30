<?php

namespace App\Model\Repository\Base;


use App\Model\DTO\ProjectUserDTO;
use Nette\Database\Table\Selection;

/**
 * Přístup k datům z tabulky project_user
 */
interface IProjectUserRepository
{

    public function getTableName(): string;

    /**
     * Přiřadí uživatele k projektu
     * @param ProjectUserDTO $projectUserDTO
     * @return void
     */
    public function saveUserToProject(ProjectUserDTO $projectUserDTO): void;

    /**
     * Vrati pole uzivatelu v roli pracovnik, kteri momentalne nepracuji na danem projektu
     * @param int $projectId
     * @return array pole ve tvaru [id => cele_jmeno]
     */
    public function getAllUsersThatDoesNotWorkOnProject(int $projectId): array;

    /**
     * Vrati selekci pro grid, ktera ukazuje vsechny projekty uzivatele
     * @param int $userId
     * @return Selection
     */
    public function getAllUserProjectGridSelection(int $userId): Selection;

    /**
     * Vrati pole vsech identifikatoru clenstvi useru na projektech
     * @param array $userIds - pole id uzivatelu ve formatu [id, id1, id2, ...]
     * @return array pole ve formátu [idCl => idCl, idCl1 => idCl1,...]
     */
    public function getAllProjectsMembershipsOfUserIds(array $userIds): array;

    /**
     * Vrati vsechny clenstvi v projektu daneho usera s $userId
     * @param int $userId
     * @return array c
     */
    public function getAllProjectMembershipIds(int $userId): array;

    /**
     * Vrati selekci pro grid, kde najde vsechny uzivatele(pracovniky) na projektu
     * @param int $projectId
     * @return Selection
     */
    public function getAllUsersOnProjectGridSelection(int $projectId): Selection;

    /**
     * Vrati pole id zaznamu, ktere reprezentuji clenstvi useru v konkretnim projektu s projektem $projectId
     * @param int $projectId
     * @return array idCl => idCl
     */
    public function getAllUsersOnProjectIds(int $projectId): array;

    /**
     * Vrati informace o vsech pracovnikach na projektu
     * @param int $projectId
     * @return array
     */
    public function getAllUsersInfoOnProject(int $projectId): array;

    /**
     * Overi, jestli uzivatel pracuje na projektu
     * @param int $userId
     * @param int $projectId
     * @return int pokud ano, tak vrati id jeho clenstvi, pokud ne, vraci -1
     */
    public function isUserOnProject(int $userId, int $projectId): int;



}