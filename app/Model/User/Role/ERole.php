<?php

namespace App\Model\User\Role;

enum ERole: int
{
    case user = 1;
    case superior = 2 ;
    case project_manager = 3;
    case department_manager = 4;
    case secretariat = 5;


}