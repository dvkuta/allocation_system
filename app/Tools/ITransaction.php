<?php

namespace App\Tools;

interface ITransaction
{

    public function begin();

    public function commit();

    public function rollback();
}
