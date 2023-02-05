<?php
namespace App\Model\Repository\Domain;

/**
 * Data value object pro uživatele. Slouží pro snazší předávání dat mezi metodami
 */
class User
{
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $login;
    private string $workplace;
    //nekdy neni vyplneno, pokud neni potreba
    private string $password;
    //při vytváření není vyplněno
    private ?int $id;
    /**
     * @var array ve tvaru [index => id_role]
     */
    private array $roles;

    /**
     * @param int|null $id
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $login
     * @param string $workplace
     * @param string $password
     * @param array $roles
     */
    public function __construct(?int $id, string $firstname, string $lastname, string $email, string $login, string $workplace , string $password = "", array $roles = [])
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->login = $login;
        $this->workplace = $workplace;
        $this->password = $password;
        $this->id = $id;
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }




    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getFullName(): string
    {
        return $this->firstname . " " . $this->lastname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getWorkplace(): string
    {
        return $this->workplace;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }










}