<?php

namespace App\Model\Project\ProjectUserAllocation;

/**
 * Stavy alokace
 */
enum EState: string
{
    case ACTIVE = 'active'; //aktivni, zapocitava se do workload
    case DRAFT = 'draft' ; //predloha, nezapocitava se
    case CANCEL = 'cancel'; //zruseny, nezapocitava se

}