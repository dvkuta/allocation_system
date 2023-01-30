<?php

namespace App\Model\Project\ProjectUser;

/**
 * Stavy projektu
 */
enum EState: string
{
    case ACTIVE = 'active'; //aktivni, zapocitava se do workload
    case DRAFT = 'draft' ; //predloha, nezapocitava se
    case CANCEL = 'cancel'; //zruseny, nezapocitava se


}