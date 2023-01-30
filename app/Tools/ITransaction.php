<?php

namespace App\Tools;

/**
 * Rozhranni pro transakce
 */
interface ITransaction
{

    public function begin();

    public function commit();

    public function rollback();
}
