<?php

namespace App\Model\Repository\Base;


use App\Model\DTO\UserDTO;


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

    public function getUser(int $id): ?UserDTO;

    public function getUserByLogin(string $login): ?UserDTO;

    /**
     * Kontrola, jestli je registrovan uzivatel se zadanym loginem
     * @param string $login
     * @return bool
     */
    public function loginExists(string $login): bool;


    /**
     * Uloží uživatele v databází a vrátí nová data pro nastavení rolí
     * @param UserDTO $user
     * @return UserDTO
     */
    public function saveUser(UserDTO $user): UserDTO;

    /**
     * Upraví uživatele v databází a vrátí nová data pro nastavení rolí
     */
    public function updateUser(UserDTO $user): UserDTO;

}