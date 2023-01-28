<?php

namespace App\Model\Project\ProjectUser;

enum EState: string
{
    case ACTIVE = 'active';
    case DRAFT = 'draft' ;
    case CANCEL = 'cancel';


}