<?php

namespace App\Model\User\Role;

enum ERole: string
{
    case USER = 'user';
    case SUPERIOR = 'superior' ;
    case PROJECT_MANAGER = 'project_manager';
    case DEPARTMENT_MANAGER = 'department_manager';
    case SECRETARIAT = 'secretariat';


}