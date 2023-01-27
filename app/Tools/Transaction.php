<?php

namespace App\Tools;

use Nette\Database\Explorer;

class Transaction implements ITransaction
{
    private Explorer $explorer;

    /**
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        $this->explorer = $explorer;
    }

    public function begin(): void
    {
        $this->explorer->beginTransaction();
    }

    public function commit(): void
    {
        $this->explorer->commit();
    }

    public function rollback(): void
    {
        $this->explorer->rollBack();
    }

}