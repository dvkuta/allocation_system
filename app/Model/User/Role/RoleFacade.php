<?php

namespace App\Model\User\Role;


use App\Model\Repository\Base\BaseRepository;
use App\Model\Repository\Base\IRoleRepository;
use App\Model\Repository\Base\IUserRoleRepository;
use Nette\Database\Explorer;


/**
 * Přístup k datům z tabulky Role
 */
class RoleFacade
{


    private IRoleRepository $roleRepository;

    public function __construct(IRoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function fetchDataForSelect(): array
    {
        return $this->roleRepository->fetchDataForSelect();
    }

}