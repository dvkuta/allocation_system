<?php
namespace App\Model\DTO;

class UserDTO
{
private string $firstname;
private string $lastname;
private string $email;
private string $login;
private string $workplace;

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $login
     * @param string $workplace
     * @param array $roles
     */
    public function __construct(string $firstname, string $lastname, string $email, string $login, string $workplace)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->login = $login;
        $this->workplace = $workplace;
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





}