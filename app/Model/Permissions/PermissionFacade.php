<?php
namespace App\Model\Permissions;
use App\Model\Project\ProjectFacade;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use App\Model\User\Role\ERole;
use Nette\Security\User;

class PermissionFacade
{


    private ProjectFacade $projectFacade;
    private ProjectUserAllocationFacade $projectUserAllocationFacade;

    public function __construct(
        ProjectFacade $projectFacade,
        ProjectUserAllocationFacade $projectUserAllocationFacade
    )
    {
        $this->projectFacade = $projectFacade;
        $this->projectUserAllocationFacade = $projectUserAllocationFacade;
    }

    /**
     * Muze uzivatel pridat projekt?
     * @param User $user
     * @return bool
     */
    public function canUserAccessProjectAdd(User $user): bool
    {
        return $user->isInRole(ERole::secretariat->name);
    }

    /**
     * Muze uzivatel editovat projekt?
     * @param User $user
     * @param int $projectId
     * @return bool
     */
    public function canUserEditCurrentProject(User $user, int $projectId): bool
    {
        if(!($user->isInRole(ERole::secretariat->name) ||
            $user->isInRole(ERole::department_manager->name) ||
            $user->isInRole(ERole::project_manager->name)))
        {
            return false;
        }

        if($user->isInRole(ERole::project_manager->name)
            && (!$user->isInRole(ERole::department_manager->name)
                && (!$user->isInRole(ERole::secretariat->name))
            )
        )
        {
            if(!$this->projectFacade->isUserManagerOfProject($user->getId(), $projectId))
            {
                return false;
            }
        }
        return true;
    }

    /**
     *      * Muze uzivatel pridat do projektu pracovnika?
     * @param User $user
     * @return bool
     */
    public function canUserAccessProjectAddUser(User $user): bool
    {
        return $user->isInRole(ERole::secretariat->name);
    }


    /**
     * Muze uzivatel pristoupit na detail projektu?
     * @param User $user
     * @param int $projectId
     * @return bool
     */
    public function canUserAccessProjectDetail(User $user, int $projectId): bool
    {
        if(!($user->isInRole(ERole::project_manager->name) ||
            $user->isInRole(ERole::department_manager->name)))
        {
            return false;
        }

        if($user->isInRole(ERole::project_manager->name)
            && (!$user->isInRole(ERole::department_manager->name))
        )
        {
            if(!$this->projectFacade->isUserManagerOfProject($user->getId(), $projectId))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Muze uzivatel editovat alokaci na projektu?
     * @param User $user
     * @param int $allocationId
     * @return bool
     */
    public function canUserEditProjectAllocation(User $user, int $allocationId): bool
    {
        if(
            !($user->isInRole(ERole::project_manager->name) || $user->isInRole(ERole::department_manager->name))
        )
        {
            return false;
        }

        if($user->isInRole(ERole::project_manager->name)
            && (!$user->isInRole(ERole::department_manager->name))
        )
        {
            if(!$this->projectUserAllocationFacade->isUserProjectManagerOfProjectOfThisAllocation($user->getId(), $allocationId))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Muze uzivatel pridat pracovnika do projektu?
     * @param User $user
     * @return bool
     */
    public function canUserAddWorkerToProject(User $user): bool
    {
        return $user->isInRole(ERole::secretariat->name);
    }

    /**
     * Muze uzivatel videt spravu projektu?
     * @param User $user
     * @return bool
     */
    public function canUserSeeProjectDefault(User $user)
    {
        return $user->isInRole(ERole::secretariat->name) ||
            $user->isInRole(ERole::project_manager->name) ||
            $user->isInRole(ERole::department_manager->name);
    }

    /**
     * Muze uzivatel editovat alokaci?
     * @param User $user
     * @param int $projectId
     * @return bool
     */
    public function canUserAddAllocation(User $user, int $projectId): bool
    {
        if(
            !(
                $user->isInRole(ERole::project_manager->name) ||
                $user->isInRole(ERole::department_manager->name)
            )
        )
        {
           return false;
        }

        if($user->isInRole(ERole::project_manager->name)
            && (!$user->isInRole(ERole::department_manager->name))
        )
        {
            if(!$this->projectFacade->isUserManagerOfProject($user->getId(), $projectId))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Muze uzivatel pristoupit do spravy uzivatelu?
     * @param User $user
     * @return bool
     */
    public function canAccessUserManagement(User $user): bool
    {
        return $user->isInRole(ERole::secretariat->name);
    }


}