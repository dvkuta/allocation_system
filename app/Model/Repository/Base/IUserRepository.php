<?php

namespace App\Model\Repository\Base;


use App\Model\Repository\Domain\User;
use Nette\Database\Table\Selection;


/**
 * Přístup k datům z tabulky user
 */
interface IUserRepository
{

    public function getTableName(): string;

    /**
     * Kontrola, jestli je registrovan uzivatel se zadanym emailem
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool;

    public function getUser(int $id): ?User;

    public function getUserByLogin(string $login): ?User;

    /**
     * Kontrola, jestli je registrovan uzivatel se zadanym loginem
     * @param string $login
     * @return bool
     */
    public function loginExists(string $login): bool;


    /**
     * Uloží uživatele v databází a vrátí nová data pro nastavení rolí
     * @param User $user
     * @return User
     */
    public function saveUser(User $user): User;

    /**
     * Upraví uživatele v databází a vrátí nová data pro nastavení rolí
     */
    public function updateUser(User $user): User;

    /**
     * Objekt selection pro komponentu gridu uživatelů
     * @return Selection
     */
    public function getAllUsersGridSelection(): Selection;

}