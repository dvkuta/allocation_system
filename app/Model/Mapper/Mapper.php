<?php
namespace App\Model\Mapper;

use App\Model\Project\ProjectUserAllocation\EState;
use App\Model\Repository\Domain\Allocation;
use App\Model\Repository\Domain\Project;
use App\Model\Repository\Domain\ProjectUser;
use App\Model\Repository\Domain\User;
use DateTime;

class Mapper
{
    /**
     * Mapuje hodnoty na alokaci
     * @param int|null $id
     * @param int|null $userProjectMembershipId
     * @param int $allocation
     * @param DateTime $from
     * @param DateTime $to
     * @param string $description
     * @param EState $state
     * @param int|null $currentProjectId
     * @param string|null $currentProjectName
     * @param int|null $currentWorkerId
     * @param string|null $currentWorkerFullName
     * @return Allocation
     */
    public static function mapAllocation(?int     $id,
                                         ?int     $userProjectMembershipId,
                                         int      $allocation,
                                         DateTime $from,
                                         DateTime $to,
                                         string   $description,
                                         EState   $state,
                                         ?int     $currentProjectId = null,
                                         ?string  $currentProjectName = null,
                                         ?int     $currentWorkerId = null,
                                         ?string  $currentWorkerFullName = null): Allocation
    {
        return new Allocation($id, $userProjectMembershipId,
           $allocation, $from, $to,
            $description, $state, $currentProjectId,
            $currentProjectName, $currentWorkerId,
            $currentWorkerFullName);
    }

    /**
     * Mapuje hodnoty na projekt
     * @param int|null $id
     * @param string $name
     * @param int $project_manager_id
     * @param string $project_manager_name
     * @param DateTime $from
     * @param DateTime|null $to
     * @param string $description
     * @return Project
     */
    public static function mapProject(?int      $id,
                                      string    $name,
                                      int       $project_manager_id,
                                      string    $project_manager_name,
                                      DateTime  $from,
                                      ?DateTime $to,
                                      string    $description): Project
    {
        return new Project($id, $name,
            $project_manager_id,
            $project_manager_name,
            $from, $to,$description);
    }


    /**
     * Mapuje hodnoty na projectUser
     * @param int $userId
     * @param int|null $projectId
     * @return ProjectUser
     */
    public static function mapProjectUser(int $userId, ?int $projectId): ProjectUser
    {
        return new ProjectUser($userId, $projectId);
    }


    /**
     * Mapuje hodnoty na Usera
     * @param int|null $id
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $login
     * @param string $workplace
     * @param string $password
     * @param array $roles
     * @return User
     */
    public static function mapUser(?int   $id,
                                   string $firstname,
                                   string $lastname,
                                   string $email,
                                   string $login,
                                   string $workplace,
                                   string $password = "",
                                   array  $roles = []): User
    {
        return new User($id, $firstname, $lastname, $email, $login, $workplace, $password, $roles);
    }



}
