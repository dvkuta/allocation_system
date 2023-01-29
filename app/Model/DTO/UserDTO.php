<?php
namespace App\Model\DTO;

class UserDTO
{
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $login;
    private string $workplace;
    private string $password;
    private int $id;

    /**
     * @param int $id
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $login
     * @param string $workplace
     * @param string $password
     */
    public function __construct(int $id, string $firstname, string $lastname, string $email, string $login, string $workplace , string $password = "")
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->login = $login;
        $this->workplace = $workplace;
        $this->password = $password;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getId(): int
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





}