<?php

namespace App\Model\User\Role;
/**
 * Role
 * Hodnota je zaroven id v DB
 */
enum ERole: int
{
    case worker = 1;
    case superior = 2 ;
    case project_manager = 3;
    case department_manager = 4;
    case secretariat = 5;


}