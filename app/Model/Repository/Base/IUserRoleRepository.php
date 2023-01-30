<?php

namespace App\Model\Repository\Base;

use App\Model\User\Role\ERole;

/**
 * Přístup k datům z tabulky UserRole
 */
interface IUserRoleRepository
{

    public function getTableName(): string;

    /**
     * @param int $user_id
     * @return array ve tvaru [id => typ]
     */
    public function findRolesForUser(int $user_id): array;

    /**
     * Vrati jmena a prijmeni vsech uzivatelu v dane roli.
     * Vraci pole, jelikoz je vyuzita pouze jako zdroj vyctu moznosti pro formulare
     * @param ERole $role
     * @return array ve formatu [id => cele jmeno]
     */
    public function getAllUsersInRole(ERole $role): array;

    public function saveUserRoles(array $roles, int $userId);


}